<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProjectMilestone;
use App\Models\Project;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\MilestoneProofImage;

class ProjectMilestoneController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'milestone_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'EstimatedDays' => 'required|integer|min:1',
            'WeightedPercentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|in:Pending,In Progress,Completed',
            'order' => 'nullable|integer',
        ]);

        // Existing totals
        $existingMilestoneDays = $project->milestones()->sum('EstimatedDays') ?? 0;
        $existingWeight = $project->milestones()->sum('WeightedPercentage') ?? 0;

        // If both totals are already at 100%, do not allow additional milestones
        if ($existingMilestoneDays >= $project->EstimatedAccomplishDays && $existingWeight >= 100) {
            $message = 'You have already allocated 100% of the project days and milestone weight. No more milestones can be added.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        // Calculate total milestone days including the new one
        $totalDays = $existingMilestoneDays + $request->EstimatedDays;

        // Validate that total doesn't exceed project EstimatedAccomplishDays
        if ($totalDays > $project->EstimatedAccomplishDays) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Total milestone days ({$totalDays}) cannot exceed project estimated accomplish days ({$project->EstimatedAccomplishDays}). Please adjust milestone durations.",
                ], 422);
            }
            return redirect()->back()
                ->with('error', "Total milestone days ({$totalDays}) cannot exceed project estimated accomplish days ({$project->EstimatedAccomplishDays}). Please adjust milestone durations.");
        }

        // Calculate total weight including the new one
        $totalWeight = $existingWeight + $request->WeightedPercentage;

        // Validate that total weight doesn't exceed 100%
        if ($totalWeight > 100) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Total milestone weight ({$totalWeight}%) cannot exceed 100%. Please adjust milestone weights.",
                ], 422);
            }
            return redirect()->back()
                ->with('error', "Total milestone weight ({$totalWeight}%) cannot exceed 100%. Please adjust milestone weights.");
        }

        // Calculate target_date based on cumulative EstimatedDays (only if project has StartDate)
        $maxOrder = $project->milestones()->max('order') ?? 0;
        $targetDate = null;
        
        if ($project->StartDate) {
            $previousMilestones = $project->milestones()->orderBy('order')->orderBy('milestone_id')->get();
            $cumulativeDays = $previousMilestones->sum('EstimatedDays') ?? 0;
            $targetDate = \Carbon\Carbon::parse($project->StartDate)->addDays($cumulativeDays + $request->EstimatedDays);
        }

        // Determine initial status based on project status and target date
        $initialStatus = $request->status ?? 'Pending';
        if (!$request->status && $project->StartDate && $targetDate) {
            $today = now()->toDateString();
            $targetDateStr = $targetDate->toDateString();
            
            // If project is On Going or Pre-Construction and target date is today or in the past, set to In Progress
            $projectStatus = $project->status->StatusName ?? '';
            if (in_array($projectStatus, ['On Going', 'Pre-Construction']) && $targetDateStr <= $today) {
                $initialStatus = 'In Progress';
            }
        }

        $milestone = $project->milestones()->create([
            'milestone_name' => $request->milestone_name,
            'description' => $request->description,
            'EstimatedDays' => $request->EstimatedDays,
            'WeightedPercentage' => $request->WeightedPercentage,
            'target_date' => $targetDate,
            'status' => $initialStatus,
            'order' => $request->order ?? ($maxOrder + 1),
            'SubmissionStatus' => 'Not Submitted',
        ]);

        // Save required items if provided
        if ($request->has('required_items') && $request->required_items) {
            $requiredItems = json_decode($request->required_items, true);
            if (is_array($requiredItems)) {
                foreach ($requiredItems as $item) {
                    \App\Models\MilestoneRequiredItem::create([
                        'milestone_id' => $milestone->milestone_id,
                        'item_id' => $item['item_id'],
                        'estimated_quantity' => $item['estimated_quantity'],
                    ]);
                }
            }
        }

        // Recalculate target dates for all subsequent milestones (only if project has StartDate)
        if ($project->StartDate) {
            $this->recalculateSubsequentMilestones($project, $milestone->milestone_id);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone created successfully.',
                'milestone' => $milestone->load('project'),
            ]);
        }

        // Always redirect back - if we're on create-milestones page, it will stay there
        return redirect()->back()
            ->with('success', 'Milestone added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $request->validate([
            'milestone_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'EstimatedDays' => 'required|integer|min:1',
            'actual_date' => 'nullable|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'order' => 'nullable|integer',
        ]);

        // Prevent milestone from going "In Progress" if project start date hasn't arrived yet
        if ($request->status === 'In Progress' && $project->StartDate) {
            $today = now()->toDateString();
            if ($project->StartDate > $today) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot set milestone to 'In Progress'. Project start date ({$project->formatted_start_date}) has not arrived yet. Project is still in Pre-Construction phase.",
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', "Cannot set milestone to 'In Progress'. Project start date ({$project->formatted_start_date}) has not arrived yet. Project is still in Pre-Construction phase.");
            }
        }

        // Sequential Milestone Validation: Check if all previous milestones are completed
        if ($request->status === 'In Progress' && $milestone->status !== 'In Progress') {
            $currentOrder = $milestone->order ?? $milestone->milestone_id;
            
            // Get all previous milestones (by order)
            $previousMilestones = $project->milestones()
                ->where('milestone_id', '!=', $milestone->milestone_id)
                ->where(function($q) use ($currentOrder, $milestone) {
                    $q->where('order', '<', $currentOrder)
                      ->orWhere(function($q2) use ($currentOrder, $milestone) {
                          $q2->where('order', '=', $currentOrder)
                             ->where('milestone_id', '<', $milestone->milestone_id);
                      });
                })
                ->get();
            
            // Check if any previous milestone is not completed
            $incompletePrevious = $previousMilestones->first(function($m) {
                return $m->status !== 'Completed';
            });
            
            if ($incompletePrevious) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot start this milestone. Previous milestone '{$incompletePrevious->milestone_name}' must be completed first. Milestones must be completed sequentially.",
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', "Cannot start this milestone. Previous milestone '{$incompletePrevious->milestone_name}' must be completed first. Milestones must be completed sequentially.");
            }
        }

        // Calculate total milestone days with updated value
        $existingMilestoneDays = $project->milestones()->where('milestone_id', '!=', $milestone->milestone_id)->sum('EstimatedDays') ?? 0;
        $totalDays = $existingMilestoneDays + $request->EstimatedDays;

        // Validate that total doesn't exceed project EstimatedAccomplishDays
        if ($totalDays > $project->EstimatedAccomplishDays) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Total milestone days ({$totalDays}) cannot exceed project estimated accomplish days ({$project->EstimatedAccomplishDays}). Please adjust milestone durations.",
                ], 422);
            }
            return redirect()->back()
                ->with('error', "Total milestone days ({$totalDays}) cannot exceed project estimated accomplish days ({$project->EstimatedAccomplishDays}). Please adjust milestone durations.");
        }

        $oldEstimatedDays = $milestone->EstimatedDays;
        
        // Prepare update data
        $updateData = $request->all();
        
        // If project doesn't have StartDate, set target_date to null
        if (!$project->StartDate) {
            $updateData['target_date'] = null;
        }
        
        $milestone->update($updateData);

        // Update required items if provided
        if ($request->has('required_items') && $request->required_items) {
            // Delete existing required items
            $milestone->requiredItems()->delete();
            
            // Add new required items
            $requiredItems = json_decode($request->required_items, true);
            if (is_array($requiredItems)) {
                foreach ($requiredItems as $item) {
                    // Validate that item_id is not null or empty
                    if (empty($item['item_id']) || $item['item_id'] === null) {
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid item data: item_id is required. Please remove and re-add the item.',
                            ], 422);
                        }
                        return redirect()->back()
                            ->with('error', 'Invalid item data: item_id is required. Please remove and re-add the item.');
                    }
                    
                    \App\Models\MilestoneRequiredItem::create([
                        'milestone_id' => $milestone->milestone_id,
                        'item_id' => $item['item_id'],
                        'estimated_quantity' => $item['estimated_quantity'],
                    ]);
                }
            }
        }

        // If EstimatedDays or order changed, recalculate target dates (only if project has StartDate)
        $oldOrder = $milestone->order ?? $milestone->milestone_id;
        $newOrder = $request->order ?? $oldOrder;
        
        if ($project->StartDate && ($oldEstimatedDays != $request->EstimatedDays || $oldOrder != $newOrder)) {
            $this->recalculateAllMilestoneDates($project);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone updated successfully.',
                'milestone' => $milestone->load('project'),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Milestone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        $milestone->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone deleted successfully.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Milestone deleted successfully.');
    }

    /**
     * Foreman submits milestone completion with proof images
     */
    public function submitCompletion(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $user = Auth::user();
        
        if (!$milestone->canUserSubmit($user)) {
            return redirect()->back()
                ->with('error', 'You are not authorized to submit this milestone completion.');
        }

        // Validate that images are required
        $request->validate([
            'proof_images' => 'required|array|min:1',
            'proof_images.*' => 'required|image|mimes:jpg,jpeg,png|max:10240', // 10MB max per image
        ], [
            'proof_images.required' => 'At least one proof image is required.',
            'proof_images.min' => 'At least one proof image is required.',
            'proof_images.*.required' => 'Each image file is required.',
            'proof_images.*.image' => 'Each file must be an image.',
            'proof_images.*.mimes' => 'Images must be in JPG, JPEG, or PNG format.',
            'proof_images.*.max' => 'Each image must not exceed 10MB.',
        ]);

        // Store images
        $uploadedImages = [];
        foreach ($request->file('proof_images') as $image) {
            $fileName = 'milestone_' . $milestone->milestone_id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('milestone_proofs', $fileName, 'public');
            
            // Create proof image record
            MilestoneProofImage::create([
                'milestone_id' => $milestone->milestone_id,
                'image_path' => $path,
            ]);
            
            $uploadedImages[] = $path;
        }

        $milestone->update([
            'SubmittedBy' => $user->EmployeeID,
            'SubmittedAt' => now(),
            'SubmissionStatus' => 'Pending Approval',
        ]);

        $imageCount = count($uploadedImages);
        $message = 'Milestone completion submitted successfully with ' . $imageCount . ' proof image(s). Waiting for engineer/general manager approval.';

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Engineer/General Manager approves milestone completion
     */
    public function approveCompletion(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $user = Auth::user();
        
        if (!$milestone->canUserApprove($user)) {
            return redirect()->back()
                ->with('error', 'You are not authorized to approve this milestone completion.');
        }

        $milestone->update([
            'ApprovedBy' => $user->EmployeeID,
            'ApprovedAt' => now(),
            'actual_date' => now(),
            'status' => 'Completed',
            'SubmissionStatus' => 'Approved',
        ]);

        return redirect()->back()
            ->with('success', 'Milestone completion approved successfully.');
    }

    /**
     * Engineer/General Manager rejects milestone completion
     */
    public function rejectCompletion(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $user = Auth::user();
        
        if (!$milestone->canUserApprove($user)) {
            return redirect()->back()
                ->with('error', 'You are not authorized to reject this milestone completion.');
        }

        // Delete proof images when rejecting
        $proofImages = $milestone->proofImages;
        foreach ($proofImages as $proofImage) {
            // Delete file from storage
            if (Storage::disk('public')->exists($proofImage->image_path)) {
                Storage::disk('public')->delete($proofImage->image_path);
            }
            // Delete record
            $proofImage->delete();
        }

        $milestone->update([
            'SubmittedBy' => null,
            'SubmittedAt' => null,
            'SubmissionStatus' => 'Not Submitted',
        ]);

        return redirect()->back()
            ->with('success', 'Milestone submission rejected. Foreman can resubmit after making corrections.');
    }

    /**
     * Recalculate target dates for all milestones after a specific milestone
     */
    private function recalculateSubsequentMilestones(Project $project, $afterMilestoneId)
    {
        if (!$project->StartDate) {
            return;
        }

        $milestones = $project->milestones()->orderBy('order')->orderBy('milestone_id')->get();
        $cumulativeDays = 0;

        foreach ($milestones as $ms) {
            if ($ms->EstimatedDays) {
                $cumulativeDays += $ms->EstimatedDays;
                $ms->target_date = Carbon::parse($project->StartDate)->addDays($cumulativeDays);
                $ms->saveQuietly();
            }
        }
    }

    /**
     * Recalculate target dates for all milestones
     */
    private function recalculateAllMilestoneDates(Project $project)
    {
        if (!$project->StartDate) {
            return;
        }

        $milestones = $project->milestones()->orderBy('order')->orderBy('milestone_id')->get();
        $cumulativeDays = 0;

        foreach ($milestones as $ms) {
            if ($ms->EstimatedDays) {
                $cumulativeDays += $ms->EstimatedDays;
                $ms->target_date = Carbon::parse($project->StartDate)->addDays($cumulativeDays);
                $ms->saveQuietly();
            }
        }
    }

    /**
     * Get required items for a milestone (excluding already issued items)
     */
    public function getRequiredItems(ProjectMilestone $milestone)
    {
        $requiredItems = $milestone->requiredItems()->with('resourceCatalog')->get();
        
        // Get issued items for this milestone
        $issuedItems = \App\Models\IssuanceRecordItem::whereHas('issuanceRecord', function($q) use ($milestone) {
            $q->where('MilestoneID', $milestone->milestone_id)
              ->where('Status', 'Issued');
        })->get()->groupBy('ItemID');
        
        return response()->json($requiredItems->map(function($req) use ($issuedItems) {
            $resourceCatalog = $req->resourceCatalog;
            
            // Find the corresponding inventory item
            $inventoryItem = null;
            if ($resourceCatalog) {
                $inventoryItem = \App\Models\InventoryItem::where('ResourceCatalogID', $resourceCatalog->ResourceCatalogID)->first();
            }
            
            // Calculate already issued quantity
            $issuedQty = 0;
            if ($inventoryItem && isset($issuedItems[$inventoryItem->ItemID])) {
                $issuedQty = $issuedItems[$inventoryItem->ItemID]->sum('QuantityIssued');
            }
            
            // Calculate remaining quantity needed
            $remainingQty = max(0, $req->estimated_quantity - $issuedQty);
            
            return [
                'ItemID' => $inventoryItem->ItemID ?? null,
                'ResourceCatalogID' => $resourceCatalog->ResourceCatalogID ?? null,
                'ItemName' => $resourceCatalog->ItemName ?? 'N/A',
                'ItemType' => $resourceCatalog->Type ?? 'N/A',
                'Unit' => $resourceCatalog->Unit ?? 'N/A',
                'estimated_quantity' => $req->estimated_quantity,
                'issued_quantity' => $issuedQty,
                'remaining_quantity' => $remainingQty,
            ];
        })->filter(function($item) {
            // Only return items that still need to be issued
            return $item['remaining_quantity'] > 0;
        })->values());
    }

    /**
     * Get proof images for a milestone
     */
    public function getProofImages(ProjectMilestone $milestone)
    {
        $proofImages = $milestone->proofImages()->orderBy('created_at', 'desc')->get();
        
        return response()->json($proofImages->map(function($image) {
            return [
                'id' => $image->id,
                'image_path' => $image->image_path,
                'created_at' => $image->created_at ? $image->created_at->format('M d, Y H:i') : null,
            ];
        }));
    }
}
