<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\ResourceCatalog;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        $status = $request->get('status');
        $lowStock = $request->get('low_stock');

        $query = InventoryItem::with('resourceCatalog');

        if ($type) {
            $query->whereHas('resourceCatalog', function($q) use ($type) {
                $q->where('Type', $type);
            });
        }

        if ($status) {
            $query->where('Status', $status);
        }

        if ($lowStock) {
            $query->where('AvailableQuantity', '<', 10)
                  ->where('Status', 'Active');
        }

        $items = $query->orderBy('ItemID', 'desc')->paginate(15);

        return view('inventory.index', compact('items', 'type', 'status', 'lowStock'));
    }

    /**
     * Note: Inventory items are now created automatically through the receiving process.
     * Items are added to inventory when purchase orders are received.
     * Use Resource Catalog to manage the master list of items.
     */

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventory)
    {
        $inventory->load(['resourceCatalog', 'milestoneMaterials.milestone.project', 'milestoneEquipment.milestone.project']);
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $inventory)
    {
        $inventory->load('resourceCatalog');
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'TotalQuantity' => 'required|numeric|min:0',
            'AvailableQuantity' => 'required|numeric|min:0|lte:TotalQuantity',
            'Status' => 'required|in:Active,Inactive',
        ]);

        $inventory->update([
            'TotalQuantity' => $request->TotalQuantity,
            'AvailableQuantity' => $request->AvailableQuantity,
            'Status' => $request->Status,
        ]);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory quantities updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventory)
    {
        // Check if item is being used in any milestones
        $hasMaterials = $inventory->milestoneMaterials()->count() > 0;
        $hasEquipment = $inventory->milestoneEquipment()->where('Status', '!=', 'Returned')->count() > 0;

        if ($hasMaterials || $hasEquipment) {
            return redirect()->route('inventory.index')
                ->with('error', 'Cannot delete item. It is currently being used in project milestones.');
        }

        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Display low stock items.
     */
    public function lowStock()
    {
        $items = InventoryItem::with('resourceCatalog')
            ->where('AvailableQuantity', '<', 10)
            ->where('Status', 'Active')
            ->orderBy('ItemID', 'desc')
            ->paginate(15);

        return view('inventory.low-stock', compact('items'));
    }
}
