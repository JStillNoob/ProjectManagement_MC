<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('SupplierName')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'SupplierName' => 'required|string|max:255',
            'ContactFirstName' => 'nullable|string|max:50',
            'ContactLastName' => 'nullable|string|max:50',
            'PhoneNumber' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'Street' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:100',
            'Province' => 'nullable|string|max:100',
            'PostalCode' => 'nullable|string|max:20',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $purchaseOrders = $supplier->purchaseOrders;
        return view('suppliers.show', compact('supplier', 'purchaseOrders'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'SupplierName' => 'required|string|max:255',
            'ContactFirstName' => 'nullable|string|max:50',
            'ContactLastName' => 'nullable|string|max:50',
            'PhoneNumber' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'Street' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:100',
            'Province' => 'nullable|string|max:100',
            'PostalCode' => 'nullable|string|max:20',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier has purchase orders
        if ($supplier->purchaseOrders()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete supplier with existing purchase orders.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
