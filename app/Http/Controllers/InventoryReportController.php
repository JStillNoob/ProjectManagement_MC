<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\IssuanceRecord;
use App\Models\ReceivingRecord;
use App\Models\ProjectMilestoneMaterial;
use App\Models\ProjectMilestoneEquipment;
use App\Models\EquipmentIncident;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    public function index()
    {
        return view('reports.inventory.index');
    }

    public function stockLevel(Request $request)
    {
        $query = InventoryItem::with('resourceCatalog')->where('Status', 'Active');

        if ($request->has('item_type') && $request->item_type != '') {
            // Filter by resource catalog type instead of ItemTypeID
            $query->whereHas('resourceCatalog', function($q) use ($request) {
                $q->where('Type', $request->item_type);
            });
        }

        if ($request->has('low_stock_only') && $request->low_stock_only) {
            $query->whereRaw('AvailableQuantity < MinimumStockLevel');
        }

        $items = $query->orderBy('ItemName')->get();

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.stock-level-pdf', compact('items'));
            return $pdf->download('stock-level-report-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.inventory.stock-level', compact('items'));
    }

    public function consumption(Request $request)
    {
        // Use default dates if not provided (last 30 days)
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'project_id' => 'nullable|exists:projects,ProjectID',
            'milestone_id' => 'nullable|exists:project_milestones,milestone_id',
        ]);
        
        // Use defaults if validation passed but values are empty
        $validated['date_from'] = $validated['date_from'] ?? $dateFrom;
        $validated['date_to'] = $validated['date_to'] ?? $dateTo;

        $query = ProjectMilestoneMaterial::with(['inventoryItem', 'milestone.project'])
            ->whereBetween('DateUsed', [$validated['date_from'], $validated['date_to']]);

        if ($request->project_id) {
            $query->whereHas('milestone', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        if ($request->milestone_id) {
            $query->where('milestone_id', $request->milestone_id);
        }

        $consumptions = $query->orderBy('DateUsed', 'desc')->get();

        // Summary
        $summary = [
            'total_items' => $consumptions->unique('ItemID')->count(),
            'total_quantity' => $consumptions->sum('QuantityUsed'),
            'total_value' => $consumptions->sum(function($item) {
                return $item->QuantityUsed * ($item->inventoryItem->UnitPrice ?? 0);
            }),
        ];

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.consumption-pdf', compact('consumptions', 'summary', 'validated'));
            return $pdf->download('consumption-report-' . date('Y-m-d') . '.pdf');
        }

        $projects = Project::orderBy('ProjectName')->get();

        return view('reports.inventory.consumption', compact('consumptions', 'summary', 'projects'));
    }

    public function equipmentUtilization(Request $request)
    {
        // Use default dates if not provided (last 30 days)
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        // Use defaults if validation passed but values are empty
        $validated['date_from'] = $validated['date_from'] ?? $dateFrom;
        $validated['date_to'] = $validated['date_to'] ?? $dateTo;

        $assignments = ProjectMilestoneEquipment::with([
            'inventoryItem',
            'milestone.project'
        ])
        ->whereBetween('DateAssigned', [$validated['date_from'], $validated['date_to']])
        ->orderBy('DateAssigned', 'desc')
        ->get();

        // Calculate utilization stats
        $utilizationStats = $assignments->groupBy('ItemID')->map(function($group) {
            $item = $group->first()->inventoryItem;
            $totalDaysUsed = $group->sum(function($assignment) {
                $start = \Carbon\Carbon::parse($assignment->DateAssigned);
                $end = $assignment->DateReturned ? \Carbon\Carbon::parse($assignment->DateReturned) : now();
                return $start->diffInDays($end);
            });

            return [
                'item' => $item,
                'times_assigned' => $group->count(),
                'total_days_used' => $totalDaysUsed,
                'currently_in_use' => $group->where('DateReturned', null)->count(),
                'projects' => $group->pluck('milestone.project.ProjectName')->unique()->values(),
            ];
        });

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.equipment-utilization-pdf', compact('utilizationStats', 'validated'));
            return $pdf->download('equipment-utilization-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.inventory.equipment-utilization', compact('utilizationStats', 'assignments'));
    }

    public function purchaseOrderSummary(Request $request)
    {
        // Use default dates if not provided (last 30 days)
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:Draft,Sent,Partially Received,Completed,Cancelled',
        ]);
        
        // Use defaults if validation passed but values are empty
        $validated['date_from'] = $validated['date_from'] ?? $dateFrom;
        $validated['date_to'] = $validated['date_to'] ?? $dateTo;

        $query = PurchaseOrder::with('supplier')
            ->whereBetween('OrderDate', [$validated['date_from'], $validated['date_to']]);

        if ($request->status) {
            $query->where('Status', $request->status);
        }

        $purchaseOrders = $query->orderBy('OrderDate', 'desc')->get();

        $summary = [
            'total_pos' => $purchaseOrders->count(),
            'total_amount' => $purchaseOrders->sum('TotalAmount'),
            'completed' => $purchaseOrders->where('Status', 'Completed')->count(),
            'pending' => $purchaseOrders->whereIn('Status', ['Sent', 'Partially Received'])->count(),
        ];

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.po-summary-pdf', compact('purchaseOrders', 'summary', 'validated'));
            return $pdf->download('po-summary-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.inventory.po-summary', compact('purchaseOrders', 'summary'));
    }

    public function issuanceHistory(Request $request)
    {
        // Use default dates if not provided (last 30 days)
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'project_id' => 'nullable|exists:projects,ProjectID',
        ]);
        
        // Use defaults if validation passed but values are empty
        $validated['date_from'] = $validated['date_from'] ?? $dateFrom;
        $validated['date_to'] = $validated['date_to'] ?? $dateTo;

        $query = IssuanceRecord::with(['project', 'issuer', 'receiver', 'items.inventoryItem'])
            ->whereBetween('IssuanceDate', [$validated['date_from'], $validated['date_to']]);

        if ($request->project_id) {
            $query->where('ProjectID', $request->project_id);
        }

        $issuances = $query->orderBy('IssuanceDate', 'desc')->get();

        $summary = [
            'total_issuances' => $issuances->count(),
            'total_items' => $issuances->sum(function($issuance) {
                return $issuance->items->sum('QuantityIssued');
            }),
            'materials_issued' => $issuances->sum(function($issuance) {
                return $issuance->items->where('ItemType', 'Material')->sum('QuantityIssued');
            }),
            'equipment_issued' => $issuances->sum(function($issuance) {
                return $issuance->items->where('ItemType', 'Equipment')->sum('QuantityIssued');
            }),
        ];

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.issuance-history-pdf', compact('issuances', 'summary', 'validated'));
            return $pdf->download('issuance-history-' . date('Y-m-d') . '.pdf');
        }

        $projects = Project::orderBy('ProjectName')->get();

        return view('reports.inventory.issuance-history', compact('issuances', 'summary', 'projects'));
    }

    public function damageReport(Request $request)
    {
        // Use default dates if not provided (last 30 days)
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'incident_type' => 'nullable|in:Damage,Loss,Theft,Malfunction',
        ]);
        
        // Use defaults if validation passed but values are empty
        $validated['date_from'] = $validated['date_from'] ?? $dateFrom;
        $validated['date_to'] = $validated['date_to'] ?? $dateTo;

        $query = EquipmentIncident::with(['inventoryItem', 'project', 'responsibleEmployee'])
            ->whereBetween('IncidentDate', [$validated['date_from'], $validated['date_to']]);

        if ($request->incident_type) {
            $query->where('IncidentType', $request->incident_type);
        }

        $incidents = $query->orderBy('IncidentDate', 'desc')->get();

        $summary = [
            'total_incidents' => $incidents->count(),
            'total_cost' => $incidents->sum('EstimatedCost'),
            'by_type' => $incidents->groupBy('IncidentType')->map->count(),
        ];

        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.inventory.damage-report-pdf', compact('incidents', 'summary', 'validated'));
            return $pdf->download('damage-report-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.inventory.damage-report', compact('incidents', 'summary'));
    }
}
