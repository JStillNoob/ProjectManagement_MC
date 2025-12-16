<?php

namespace App\Http\Controllers;

use App\Models\ResourceCatalog;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceCatalogController extends Controller
{
    /**
     * Return all resource catalog items as JSON (for API usage)
     */
    public function items()
    {
        $items = ResourceCatalog::orderBy('ItemName')->get();
        return response()->json($items);
    }
    /**
     * Display a listing of the resource catalog
     */
    public function index(Request $request)
    {
        $query = ResourceCatalog::query();

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('Type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('ItemName', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('ItemName')->get();

        return view('resource-catalog.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        // Not used - creating is done via modal
        return redirect()->route('resource-catalog.index');
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ItemName' => 'required|string|max:255|unique:resource_catalog,ItemName',
            'Unit' => 'required|string|max:50',
            'Type' => 'required|in:Equipment,Materials',
        ]);

        DB::beginTransaction();
        try {
            // Create resource catalog item
            $resourceCatalog = ResourceCatalog::create($validated);

            // Automatically create corresponding inventory item with zero quantity
            InventoryItem::firstOrCreate(
                ['ResourceCatalogID' => $resourceCatalog->ResourceCatalogID],
                [
                    'TotalQuantity' => 0,
                    'AvailableQuantity' => 0,
                    'CommittedQuantity' => 0,
                    'MinimumStockLevel' => 0,
                    'UnitPrice' => 0,
                    'Status' => 'Active',
                ]
            );

            DB::commit();

            return redirect()->route('resource-catalog.index')
                ->with('success', 'Resource added to catalog successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('resource-catalog.index')
                ->with('error', 'Failed to add resource: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        $resourceCatalog = ResourceCatalog::findOrFail($id);
        $resourceCatalog->load('inventoryItems');
        return view('resource-catalog.show', compact('resourceCatalog'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit($id)
    {
        // Not used - editing is done via modal
        return redirect()->route('resource-catalog.index');
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ItemName' => 'required|string|max:255|unique:resource_catalog,ItemName,' . $id . ',ResourceCatalogID',
            'Unit' => 'required|string|max:50',
            'Type' => 'required|in:Equipment,Materials',
        ]);

        $resourceCatalog = ResourceCatalog::findOrFail($id);
        $resourceCatalog->update($validated);

        return redirect()->route('resource-catalog.index')
            ->with('success', 'Resource updated successfully');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($id)
    {
        try {
            $resourceCatalog = ResourceCatalog::findOrFail($id);
            $resourceCatalog->delete();
            return redirect()->route('resource-catalog.index')
                ->with('success', 'Resource deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('resource-catalog.index')
                ->with('error', 'Cannot delete resource. It may be in use.');
        }
    }
}
