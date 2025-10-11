<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Benefit;
use App\Models\EmployeeBenefit;
use App\Models\EmployeeType;

class EmployeeBenefitService
{
    /**
     * Automatically assign benefits to an employee based on their employee type
     *
     * @param Employee $employee
     * @return array
     */
    public function assignBenefitsBasedOnType(Employee $employee): array
    {
        $assignedBenefits = [];
        
        if (!$employee->employeeType || !$employee->employeeType->hasBenefits) {
            return $assignedBenefits;
        }

        // Get all active benefits
        $benefits = Benefit::active()->get();
        
        foreach ($benefits as $benefit) {
            // Check if employee already has this benefit
            $existingBenefit = $employee->employeeBenefits()
                ->where('BenefitID', $benefit->BenefitID)
                ->where('IsActive', true)
                ->first();

            if (!$existingBenefit) {
                // Assign the benefit
                $employeeBenefit = $employee->employeeBenefits()->create([
                    'BenefitID' => $benefit->BenefitID,
                    'EffectiveDate' => now(),
                    'Amount' => $benefit->Amount,
                    'Percentage' => $benefit->Percentage,
                    'IsActive' => true,
                ]);

                $assignedBenefits[] = [
                    'benefit' => $benefit,
                    'employeeBenefit' => $employeeBenefit
                ];
            }
        }

        return $assignedBenefits;
    }

    /**
     * Remove all benefits from an employee
     *
     * @param Employee $employee
     * @return bool
     */
    public function removeAllBenefits(Employee $employee): bool
    {
        return $employee->employeeBenefits()
            ->where('IsActive', true)
            ->update(['IsActive' => false, 'ExpiryDate' => now()]);
    }

    /**
     * Calculate total benefit cost for an employee
     *
     * @param Employee $employee
     * @param float $baseSalary
     * @return array
     */
    public function calculateBenefitCosts(Employee $employee, float $baseSalary): array
    {
        $benefits = $employee->getCurrentBenefits();
        $totalCost = 0;
        $benefitBreakdown = [];

        foreach ($benefits as $employeeBenefit) {
            $benefit = $employeeBenefit->benefit;
            $cost = 0;

            if ($benefit->BenefitType === 'Fixed') {
                $cost = $employeeBenefit->Amount ?? $benefit->Amount ?? 0;
            } elseif ($benefit->BenefitType === 'Percentage') {
                $percentage = $employeeBenefit->Percentage ?? $benefit->Percentage ?? 0;
                $cost = ($baseSalary * $percentage) / 100;
            }

            $benefitBreakdown[] = [
                'benefit_name' => $benefit->BenefitName,
                'type' => $benefit->BenefitType,
                'cost' => $cost,
                'amount' => $employeeBenefit->Amount ?? $benefit->Amount,
                'percentage' => $employeeBenefit->Percentage ?? $benefit->Percentage,
            ];

            $totalCost += $cost;
        }

        return [
            'total_cost' => $totalCost,
            'benefit_breakdown' => $benefitBreakdown,
            'net_salary' => $baseSalary - $totalCost
        ];
    }

    /**
     * Get employees by benefit eligibility
     *
     * @param bool $eligible
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEmployeesByBenefitEligibility(bool $eligible = true)
    {
        $query = Employee::with(['employeeType', 'employeeBenefits.benefit']);

        if ($eligible) {
            $query->whereHas('employeeType', function ($q) {
                $q->where('hasBenefits', true);
            });
        } else {
            $query->whereHas('employeeType', function ($q) {
                $q->where('hasBenefits', false);
            });
        }

        return $query->get();
    }

    /**
     * Update employee type and automatically adjust benefits
     *
     * @param Employee $employee
     * @param EmployeeType $newEmployeeType
     * @return array
     */
    public function updateEmployeeTypeWithBenefits(Employee $employee, EmployeeType $newEmployeeType): array
    {
        $result = [
            'employee_updated' => false,
            'benefits_assigned' => [],
            'benefits_removed' => false
        ];

        // Update employee type
        $employee->update(['EmployeeTypeID' => $newEmployeeType->EmployeeTypeID]);
        $result['employee_updated'] = true;

        // If new type doesn't have benefits, remove all current benefits
        if (!$newEmployeeType->hasBenefits) {
            $this->removeAllBenefits($employee);
            $result['benefits_removed'] = true;
        } else {
            // If new type has benefits, assign them
            $assignedBenefits = $this->assignBenefitsBasedOnType($employee);
            $result['benefits_assigned'] = $assignedBenefits;
        }

        return $result;
    }

    /**
     * Get benefit statistics
     *
     * @return array
     */
    public function getBenefitStatistics(): array
    {
        $totalEmployees = Employee::count();
        $eligibleEmployees = Employee::whereHas('employeeType', function ($q) {
            $q->where('hasBenefits', true);
        })->count();
        
        $totalBenefits = Benefit::active()->count();
        $totalActiveAssignments = EmployeeBenefit::active()->count();

        return [
            'total_employees' => $totalEmployees,
            'eligible_employees' => $eligibleEmployees,
            'non_eligible_employees' => $totalEmployees - $eligibleEmployees,
            'total_benefits' => $totalBenefits,
            'total_active_assignments' => $totalActiveAssignments,
            'average_benefits_per_employee' => $eligibleEmployees > 0 ? round($totalActiveAssignments / $eligibleEmployees, 2) : 0
        ];
    }
}

