<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
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
        $regularType = EmployeeType::where('EmployeeTypeName', 'Regular')->first();
        $employees = Employee::with('employeeType')
            ->where('EmployeeTypeID', $regularType->EmployeeTypeID)
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
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
            'age' => 'required|integer|min:18|max:100',
            'address' => 'required|string',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:birthday',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Set employee type to Regular
        $regularType = EmployeeType::where('EmployeeTypeName', 'Regular')->first();
        $data['EmployeeTypeID'] = $regularType->EmployeeTypeID;
        $data['status'] = 'Active';

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        // Generate QR code data
        $qrData = [
            'employee_id' => 'EMP-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'name' => $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name'],
            'position' => $data['position'],
            'start_date' => $data['start_date'],
            'type' => 'Regular Employee'
        ];

        $qrDataString = json_encode($qrData);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrDataString);
        $data['qr_code'] = $qrCodeUrl;

        Employee::create($data);

        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee created successfully.');
    }

    /**
     * Display the specified regular employee.
     */
    public function show(Employee $employee)
    {
        $employee->load('employeeType');
        return view('Admin.employees.regular.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified regular employee.
     */
    public function edit(Employee $employee)
    {
        return view('Admin.employees.regular.edit', compact('employee'));
    }

    /**
     * Update the specified regular employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'age' => 'integer|min:18|max:100',
            'address' => 'required|string',
            'position' => 'required|string|max:255',
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
            if ($employee->image_name) {
                Storage::delete('public/' . $employee->image_name);
            }
            $imagePath = $request->file('image')->store('employee_images', 'public');
            $data['image_name'] = $imagePath;
        }

        $employee->update($data);

        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee updated successfully.');
    }

    /**
     * Remove the specified regular employee.
     */
    public function destroy(Employee $employee)
    {
        $employee->update(['flag_deleted' => 1]);
        return redirect()->route('regular-employees.index')
            ->with('success', 'Regular employee deleted successfully.');
    }
}