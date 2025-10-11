<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Services\EmployeeBenefitService;
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
        $employees = Employee::with('employeeType')->active()->orderBy('created_at', 'desc')->paginate(10);
        return view('Admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employeeTypes = EmployeeType::all();
        return view('Admin.employees.create', compact('employeeTypes'));
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
            'age' => 'required|integer|min:18|max:100',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'position' => 'required|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
            'EmployeeTypeID' => 'required|exists:employee_types,EmployeeTypeID',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle image upload
        $imagePath = null;
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
            'status' => $data['status']
        ];

        // Generate QR code using online API
        $qrDataString = json_encode($qrData);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrDataString);
        
        // Store QR code URL in database
        $data['qr_code'] = $qrCodeUrl;

        $employee = Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully. You can now assign benefits manually if needed.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['employeeType', 'employeeBenefits.benefit']);
        $benefitService = new EmployeeBenefitService();
        $benefitCosts = null;
        
        if ($employee->isEligibleForBenefits() && $employee->monthly_salary) {
            $benefitCosts = $benefitService->calculateBenefitCosts($employee, $employee->monthly_salary);
        }
        
        return view('Admin.employees.show', compact('employee', 'benefitCosts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employeeTypes = EmployeeType::all();
        return view('Admin.employees.edit', compact('employee', 'employeeTypes'));
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
            'age' => 'integer|min:18|max:100',
            'house_number' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'position' => 'required|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:birthday',
            'EmployeeTypeID' => 'required|exists:employee_types,EmployeeTypeID',
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
            ->with('success', 'Employee updated successfully. Please review and update benefits manually if employee type changed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Soft delete by setting flag_deleted to 1
        $employee->update(['flag_deleted' => 1]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Show employee benefits management page
     */
    public function benefits(Employee $employee)
    {
        $employee->load(['employeeType', 'employeeBenefits.benefit']);
        $allBenefits = \App\Models\Benefit::active()->get();
        $benefitService = new EmployeeBenefitService();
        $benefitCosts = null;
        
        if ($employee->isEligibleForBenefits() && $employee->monthly_salary) {
            $benefitCosts = $benefitService->calculateBenefitCosts($employee, $employee->monthly_salary);
        }
        
        return view('Admin.employees.benefits', compact('employee', 'allBenefits', 'benefitCosts'));
    }

    /**
     * Assign benefit to employee
     */
    public function assignBenefit(Request $request, Employee $employee)
    {
        $request->validate([
            'benefit_id' => 'required|exists:benefits,BenefitID',
            'amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $benefit = \App\Models\Benefit::find($request->benefit_id);
        
        // Check if employee already has this benefit
        $existingBenefit = $employee->employeeBenefits()
            ->where('BenefitID', $benefit->BenefitID)
            ->where('IsActive', true)
            ->first();

        if ($existingBenefit) {
            return redirect()->back()
                ->with('error', 'Employee already has this benefit assigned.');
        }

        // Assign the benefit
        $employee->employeeBenefits()->create([
            'BenefitID' => $benefit->BenefitID,
            'EffectiveDate' => now(),
            'Amount' => $request->amount ?? $benefit->Amount,
            'Percentage' => $request->percentage ?? $benefit->Percentage,
            'IsActive' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Benefit assigned successfully.');
    }

    /**
     * Remove benefit from employee
     */
    public function removeBenefit(Employee $employee, $benefitId)
    {
        $employeeBenefit = $employee->employeeBenefits()
            ->where('BenefitID', $benefitId)
            ->where('IsActive', true)
            ->first();

        if ($employeeBenefit) {
            $employeeBenefit->update([
                'IsActive' => false,
                'ExpiryDate' => now()
            ]);

            return redirect()->back()
                ->with('success', 'Benefit removed successfully.');
        }

        return redirect()->back()
            ->with('error', 'Benefit not found.');
    }
}