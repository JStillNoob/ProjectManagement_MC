<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OnCallEmployeeController extends Controller
{
    /**
     * Display a listing of on-call employees.
     */
    public function index()
    {
        $onCallType = EmployeeType::where('EmployeeTypeName', 'On-call')->first();
        $employees = Employee::with(['employeeType', 'position'])
            ->where('EmployeeTypeID', $onCallType->EmployeeTypeID)
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('Admin.employees.oncall.index', compact('employees'));
    }

    /**
     * Show the form for creating a new on-call employee.
     */
    public function create()
    {
        return view('Admin.employees.oncall.create');
    }

    /**
     * Store a newly created on-call employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'age' => 'required|integer|min:18|max:100',
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
        
        // Set employee type to On-call
        $onCallType = EmployeeType::where('EmployeeTypeName', 'On-call')->first();
        $data['EmployeeTypeID'] = $onCallType->EmployeeTypeID;
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
            'type' => 'On-call Employee'
        ];

        $qrDataString = json_encode($qrData);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrDataString);
        $data['qr_code'] = $qrCodeUrl;

        Employee::create($data);

        return redirect()->route('oncall-employees.index')
            ->with('success', 'On-call employee created successfully.');
    }

    /**
     * Display the specified on-call employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['employeeType', 'position']);
        return view('Admin.employees.oncall.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified on-call employee.
     */
    public function edit(Employee $employee)
    {
        return view('Admin.employees.oncall.edit', compact('employee'));
    }

    /**
     * Update the specified on-call employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'age' => 'integer|min:18|max:100',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'PositionID' => 'nullable|exists:positions,PositionID',
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
            if ($employee->image_name) {
                Storage::delete('public/' . $employee->image_name);
            }
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        $employee->update($data);

        return redirect()->route('oncall-employees.index')
            ->with('success', 'On-call employee updated successfully.');
    }

    /**
     * Remove the specified on-call employee.
     */
    public function destroy(Employee $employee)
    {
        $employee->update(['flag_deleted' => 1]);
        return redirect()->route('oncall-employees.index')
            ->with('success', 'On-call employee deleted successfully.');
    }
}