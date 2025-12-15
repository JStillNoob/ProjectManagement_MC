<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\IssuanceRecord;
use App\Models\ProjectMilestoneMaterial;
use App\Models\ProjectMilestoneEquipment;
use App\Models\EquipmentIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryDashboardController extends Controller
{
    public function index()
    {
        // Stock Overview
        $totalItems = InventoryItem::where('Status', 'Active')->count();
        $totalStock = InventoryItem::where('Status', 'Active')->sum('TotalQuantity');
        $availableStock = InventoryItem::where('Status', 'Active')->sum('AvailableQuantity');
        $committedStock = InventoryItem::where('Status', 'Active')->sum('CommittedQuantity');
        $lowStockCount = InventoryItem::whereRaw('AvailableQuantity < MinimumStockLevel')
            ->where('Status', 'Active')
            ->count();

        // Critical Alerts
        $criticalItems = InventoryItem::with('resourceCatalog')
            ->whereRaw('AvailableQuantity < MinimumStockLevel')
            ->where('Status', 'Active')
            ->orderBy('AvailableQuantity')
            ->limit(10)
            ->get();

        // Material Stock Levels
        $materials = InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Materials');
            })
            ->where('Status', 'Active')
            ->get();

        // Equipment Status
        $equipmentStats = [
            'available' => InventoryItem::whereHas('resourceCatalog', function($q) {
                    $q->where('Type', 'Equipment');
                })
                ->where('Status', 'Active')
                ->where('AvailableQuantity', '>', 0)
                ->count(),
            'in_use' => InventoryItem::whereHas('resourceCatalog', function($q) {
                    $q->where('Type', 'Equipment');
                })
                ->where('Status', 'Active')
                ->where('CommittedQuantity', '>', 0)
                ->count(),
            'damaged' => EquipmentIncident::where('Status', '!=', 'Closed')
                ->whereIn('IncidentType', ['Damage', 'Malfunction'])
                ->count(),
            'missing' => EquipmentIncident::where('Status', '!=', 'Closed')
                ->whereIn('IncidentType', ['Loss', 'Theft'])
                ->count(),
        ];

        // Incoming POs
        $incomingPOs = PurchaseOrder::with('supplier')
            ->whereIn('Status', ['Sent', 'Partially Received'])
            ->orderBy('ExpectedDeliveryDate')
            ->limit(5)
            ->get();

        // Recent Activity
        $recentIssuances = IssuanceRecord::with(['project', 'receiver'])
            ->orderBy('IssuanceDate', 'desc')
            ->limit(5)
            ->get();

        // Consumption Data for Charts (Last 30 days)
        $consumptionData = $this->getConsumptionData();
        
        return view('inventory.dashboard.index', compact(
            'totalItems',
            'totalStock',
            'availableStock',
            'committedStock',
            'lowStockCount',
            'criticalItems',
            'materials',
            'equipmentStats',
            'incomingPOs',
            'recentIssuances',
            'consumptionData'
        ));
    }

    private function getConsumptionData()
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        $dailyConsumption = ProjectMilestoneMaterial::where('DateUsed', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(DateUsed) as date'),
                DB::raw('COUNT(DISTINCT ItemID) as items_count'),
                DB::raw('SUM(QuantityUsed) as total_quantity')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $dailyConsumption->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            }),
            'quantities' => $dailyConsumption->pluck('total_quantity'),
            'items' => $dailyConsumption->pluck('items_count'),
        ];
    }

    public function materials()
    {
        $materials = InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Materials');
            })
            ->where('Status', 'Active')
            ->get();

        return view('inventory.dashboard.materials', compact('materials'));
    }

    public function equipment()
    {
        $equipment = InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Equipment');
            })
            ->where('Status', 'Active')
            ->get();

        $assignments = ProjectMilestoneEquipment::with([
            'inventoryItem',
            'milestone.project'
        ])
        ->whereNull('DateReturned')
        ->orderBy('DateAssigned', 'desc')
        ->get();

        return view('inventory.dashboard.equipment', compact('equipment', 'assignments'));
    }

    public function alerts()
    {
        $lowStockItems = InventoryItem::with('resourceCatalog')
            ->whereRaw('AvailableQuantity < MinimumStockLevel')
            ->where('Status', 'Active')
            ->orderBy('AvailableQuantity')
            ->get();

        $overdueEquipment = ProjectMilestoneEquipment::with([
            'inventoryItem',
            'milestone.project'
        ])
        ->whereNull('DateReturned')
        ->where('DateAssigned', '<', now()->subDays(30))
        ->get();

        $activeIncidents = EquipmentIncident::with(['inventoryItem', 'project'])
            ->where('Status', '!=', 'Closed')
            ->orderBy('IncidentDate', 'desc')
            ->get();

        return view('inventory.dashboard.alerts', compact(
            'lowStockItems',
            'overdueEquipment',
            'activeIncidents'
        ));
    }
}
