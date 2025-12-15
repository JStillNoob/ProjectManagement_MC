<?php

namespace App\Http\Controllers;

use App\Models\MilestoneResourcePlan;
use App\Models\ProjectMilestone;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilestoneResourceController extends Controller
{
    public function index($milestoneId)
    {
        $milestone = ProjectMilestone::with('project')->findOrFail($milestoneId);
        $resourcePlans = MilestoneResourcePlan::with('inventoryItem')
            ->where('milestone_id', $milestoneId)
            ->orderBy('NeededDate')
            ->get();

        return view('milestones.resources.index', compact('milestone', 'resourcePlans'));
    }

    public function create($milestoneId)
    {
        $milestone = ProjectMilestone::with('project')->findOrFail($milestoneId);
        $inventoryItems = InventoryItem::where('Status', 'Active')
            ->orderBy('ItemName')
            ->get();

        return view('milestones.resources.create', compact('milestone', 'inventoryItems'));
    }

    public function store(Request $request, $milestoneId)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.ItemID' => 'required|exists:inventory_items,ItemID',
            'items.*.PlannedQuantity' => 'required|integer|min:1',
            'items.*.NeededDate' => 'required|date',
            'items.*.WorkDescription' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $inventoryItem = InventoryItem::find($itemData['ItemID']);
                
                MilestoneResourcePlan::create([
                    'milestone_id' => $milestoneId,
                    'ItemID' => $itemData['ItemID'],
                    'PlannedQuantity' => $itemData['PlannedQuantity'],
                    'Unit' => $inventoryItem->Unit,
                    'NeededDate' => $itemData['NeededDate'],
                    'ResourceType' => $inventoryItem->is_equipment ? 'Equipment' : 'Material',
                    'WorkDescription' => $itemData['WorkDescription'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('milestones.resources.index', $milestoneId)
                ->with('success', 'Resource plans added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding resource plans: ' . $e->getMessage());
        }
    }

    public function destroy($milestoneId, $planId)
    {
        $plan = MilestoneResourcePlan::where('milestone_id', $milestoneId)
            ->where('PlanID', $planId)
            ->firstOrFail();

        $plan->delete();

        return back()->with('success', 'Resource plan deleted.');
    }

    public function generateRequest($planId)
    {
        $plan = MilestoneResourcePlan::with(['milestone.project', 'inventoryItem'])
            ->findOrFail($planId);

        // Redirect to inventory request creation with pre-filled data
        return redirect()->route('inventory.requests.create', [
            'project_id' => $plan->milestone->project_id,
            'milestone_id' => $plan->milestone_id,
            'item_id' => $plan->ItemID,
            'quantity' => $plan->PlannedQuantity
        ]);
    }
}
