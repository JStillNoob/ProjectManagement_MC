<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show all employees including archived
        // Eager load relationships needed for status calculation
        $employees = Employee::with(['position', 'employeeStatus'])->orderBy('created_at', 'desc')->get();
        return view('Admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
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
            'contact_number' => 'nullable|string|max:255',
            'PositionID' => 'required|exists:positions,PositionID',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Set default status to Inactive (not assigned to any project)
        $data['employee_status_id'] = EmployeeStatus::INACTIVE;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['position', 'employeeStatus']);
        
        return view('Admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('Admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
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
            'contact_number' => 'nullable|string|max:255',
            'PositionID' => 'required|exists:positions,PositionID',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
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
            // Delete old image
            if ($employee->image_name) {
                Storage::delete('public/' . $employee->image_name);
            }

            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        $employee->update($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage (Archive).
     */
    public function destroy(Employee $employee)
    {
        // Archive the employee by setting status to Archived
        $employee->update(['employee_status_id' => EmployeeStatus::ARCHIVED]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee archived successfully.');
    }

    /**
     * Unarchive the specified employee.
     */
    public function unarchive(Employee $employee)
    {
        // Unarchive the employee by setting status to Inactive
        $employee->update(['employee_status_id' => EmployeeStatus::INACTIVE]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee unarchived successfully.');
    }

}
