<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegularEmployeeController extends Controller
{
    /**
     * Display a listing of regular employees.
     */
    public function index()
    {
        $employees = Employee::with(['position'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('Admin.employees.regular.index', compact('employees'));
    }

    /**
     * Show the form for creating a new regular employee.
     */
    public function create()
    {
        return view('Admin.employees.regular.create');
    }

    /**
     * Store a newly created regular employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'PositionID' => 'required|exists:positions,PositionID',
            'base_salary' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
            'contact_number' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status'] = 'Active';

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        // Generate QR code data
        $position = \App\Models\Position::find($data['PositionID']);
        $qrData = [
            'employee_id' => 'EMP-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'name' => $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name'],
            'position' => $position ? $position->PositionName : 'Not assigned',
            'start_date' => $data['start_date'],
            'type' => 'Regular Employee'
        ];

        // Don't set qr_code here - let the model generate it automatically
        $employee = Employee::create($data);

        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee created successfully.')
            ->with('show_qr_modal', true)
            ->with('employee_data', [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'position' => $employee->position,
                'qr_code' => $employee->qr_code
            ]);
    }

    /**
     * Display the specified regular employee.
     */
    public function show(Employee $regular_employee)
    {
        $regular_employee->load(['position']);
        return view('Admin.employees.regular.show', compact('regular_employee'));
    }

    /**
     * Show the form for editing the specified regular employee.
     */
    public function edit(Employee $regular_employee)
    {
        $regular_employee->load(['position']);
        return view('Admin.employees.regular.edit', compact('regular_employee'));
    }

    /**
     * Update the specified regular employee.
     */
    public function update(Request $request, Employee $regular_employee)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'PositionID' => 'required|exists:positions,PositionID',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
            'contact_number' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($regular_employee->image_name) {
                Storage::delete('public/' . $regular_employee->image_name);
            }
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        $regular_employee->update($data);

        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee updated successfully.');
    }

    /**
     * Remove the specified regular employee.
     */
    public function destroy(Employee $regular_employee)
    {
        $regular_employee->update(['flag_deleted' => 1]);
        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee deleted successfully.');
    }
}