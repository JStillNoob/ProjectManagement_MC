<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectMilestone;
use App\Models\Project;
use App\Models\MilestoneRequiredItem;
use App\Models\IssuanceRecordItem;
use App\Models\IssuanceRecord;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class MilestoneReportController extends Controller
{
    /**
     * Check if user is Admin or Engineer
     */
    private function checkAccess()
    {
        $user = Auth::user();
        
        // Check if Admin (UserTypeID == 2)
        if (ProjectMilestone::isAdmin($user)) {
            return true;
        }
        
        // Check if Engineer
        if ($user->EmployeeID) {
            $employee = Employee::with('position')->find($user->EmployeeID);
            if ($employee && ProjectMilestone::isEngineer($employee)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Display a listing of completed milestones
     */
    public function index(Request $request)
    {
        if (!$this->checkAccess()) {
            abort(403, 'Access denied. Only Admins and Engineers can access milestone reports.');
        }

        $query = ProjectMilestone::with(['project', 'requiredItems.resourceCatalog'])
            ->where('status', 'Completed');

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $milestones = $query->orderBy('actual_date', 'desc')
            ->orderBy('milestone_id', 'desc')
            ->paginate(15);

        $projects = Project::orderBy('ProjectName')->get();

        return view('reports.milestones.index', compact('milestones', 'projects'));
    }

    /**
     * Display detailed milestone report with required vs actual items comparison
     */
    public function show(ProjectMilestone $milestone)
    {
        if (!$this->checkAccess()) {
            abort(403, 'Access denied. Only Admins and Engineers can access milestone reports.');
        }

        if ($milestone->status !== 'Completed') {
            return redirect()->route('reports.milestones.index')
                ->with('error', 'Only completed milestones can be viewed in reports.');
        }

        // Load milestone with relationships
        $milestone->load([
            'project',
            'requiredItems.resourceCatalog',
            'project.client'
        ]);

        // Get required items
        $requiredItems = $milestone->requiredItems()
            ->with('resourceCatalog')
            ->get();

        // Get actual issued items for this milestone
        $issuedItems = IssuanceRecordItem::whereHas('issuanceRecord', function($q) use ($milestone) {
            $q->where('MilestoneID', $milestone->milestone_id)
              ->where('Status', 'Issued');
        })
        ->with('inventoryItem.resourceCatalog')
        ->get()
        ->groupBy(function($item) {
            // Group by ResourceCatalogID to match with required items
            if ($item->inventoryItem && $item->inventoryItem->resourceCatalog) {
                return $item->inventoryItem->resourceCatalog->ResourceCatalogID;
            }
            // Fallback to ItemID if ResourceCatalog not found
            return $item->ItemID;
        });

        // Build comparison data
        $comparisonData = [];
        
        foreach ($requiredItems as $required) {
            $resourceCatalogId = $required->item_id; // This is ResourceCatalogID
            $resourceCatalog = $required->resourceCatalog;
            
            if (!$resourceCatalog) {
                continue;
            }

            $requiredQty = (float) $required->estimated_quantity;
            
            // Get actual quantity issued
            $actualQty = 0;
            if (isset($issuedItems[$resourceCatalogId])) {
                $actualQty = $issuedItems[$resourceCatalogId]->sum(function($item) {
                    return (float) $item->QuantityIssued;
                });
            }

            // Calculate variance and percentage
            $variance = $actualQty - $requiredQty;
            $percentage = $requiredQty > 0 ? ($actualQty / $requiredQty) * 100 : 0;
            
            // Determine status color
            $statusColor = 'success'; // Green (within ±5%)
            $statusText = 'On Target';
            
            $variancePercent = abs($percentage - 100);
            if ($variancePercent > 20) {
                $statusColor = 'danger'; // Red (>20% variance)
                $statusText = $variance > 0 ? 'Over Used' : 'Under Used';
            } elseif ($variancePercent > 5) {
                $statusColor = 'warning'; // Yellow (5-20% variance)
                $statusText = $variance > 0 ? 'Slightly Over' : 'Slightly Under';
            }

            $comparisonData[] = [
                'item_name' => $resourceCatalog->ItemName ?? 'N/A',
                'item_type' => $resourceCatalog->Type ?? 'N/A',
                'unit' => $resourceCatalog->Unit ?? 'N/A',
                'required_qty' => $requiredQty,
                'actual_qty' => $actualQty,
                'variance' => $variance,
                'percentage' => $percentage,
                'status_color' => $statusColor,
                'status_text' => $statusText,
            ];
        }

        // Sort by item name
        usort($comparisonData, function($a, $b) {
            return strcmp($a['item_name'], $b['item_name']);
        });

        return view('reports.milestones.show', compact('milestone', 'comparisonData'));
    }

    /**
     * Export milestone report as PDF
     */
    public function exportPdf(ProjectMilestone $milestone)
    {
        if (!$this->checkAccess()) {
            abort(403, 'Access denied. Only Admins and Engineers can access milestone reports.');
        }

        if ($milestone->status !== 'Completed') {
            return redirect()->route('reports.milestones.index')
                ->with('error', 'Only completed milestones can be exported.');
        }

        // Load milestone with relationships
        $milestone->load([
            'project',
            'requiredItems.resourceCatalog',
            'project.client'
        ]);

        // Get required items
        $requiredItems = $milestone->requiredItems()
            ->with('resourceCatalog')
            ->get();

        // Get actual issued items for this milestone
        $issuedItems = IssuanceRecordItem::whereHas('issuanceRecord', function($q) use ($milestone) {
            $q->where('MilestoneID', $milestone->milestone_id)
              ->where('Status', 'Issued');
        })
        ->with('inventoryItem.resourceCatalog')
        ->get()
        ->groupBy(function($item) {
            // Group by ResourceCatalogID to match with required items
            if ($item->inventoryItem && $item->inventoryItem->resourceCatalog) {
                return $item->inventoryItem->resourceCatalog->ResourceCatalogID;
            }
            // Fallback to ItemID if ResourceCatalog not found
            return $item->ItemID;
        });

        // Build comparison data (same logic as show method)
        $comparisonData = [];
        
        foreach ($requiredItems as $required) {
            $resourceCatalogId = $required->item_id;
            $resourceCatalog = $required->resourceCatalog;
            
            if (!$resourceCatalog) {
                continue;
            }

            $requiredQty = (float) $required->estimated_quantity;
            
            $actualQty = 0;
            if (isset($issuedItems[$resourceCatalogId])) {
                $actualQty = $issuedItems[$resourceCatalogId]->sum(function($item) {
                    return (float) $item->QuantityIssued;
                });
            }

            $variance = $actualQty - $requiredQty;
            $percentage = $requiredQty > 0 ? ($actualQty / $requiredQty) * 100 : 0;
            
            $statusColor = 'success';
            $statusText = 'On Target';
            
            $variancePercent = abs($percentage - 100);
            if ($variancePercent > 20) {
                $statusColor = 'danger';
                $statusText = $variance > 0 ? 'Over Used' : 'Under Used';
            } elseif ($variancePercent > 5) {
                $statusColor = 'warning';
                $statusText = $variance > 0 ? 'Slightly Over' : 'Slightly Under';
            }

            $comparisonData[] = [
                'item_name' => $resourceCatalog->ItemName ?? 'N/A',
                'item_type' => $resourceCatalog->Type ?? 'N/A',
                'unit' => $resourceCatalog->Unit ?? 'N/A',
                'required_qty' => $requiredQty,
                'actual_qty' => $actualQty,
                'variance' => $variance,
                'percentage' => $percentage,
                'status_color' => $statusColor,
                'status_text' => $statusText,
            ];
        }

        usort($comparisonData, function($a, $b) {
            return strcmp($a['item_name'], $b['item_name']);
        });

        $pdf = Pdf::loadView('reports.milestones.pdf', compact('milestone', 'comparisonData'));
        $filename = 'Milestone_Report_' . str_replace(' ', '_', $milestone->milestone_name) . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
