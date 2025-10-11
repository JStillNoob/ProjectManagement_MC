@extends('layouts.app')

@section('title', 'Employee Benefits Management')

@section('page-title', 'Employee Benefits Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Benefits for {{ $employee->full_name }}
                        <small class="text-muted">({{ $employee->employeeType->EmployeeTypeName }})</small>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-secondary"
                           style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-arrow-left"></i> Back to Employee
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Employee Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Employee Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Position:</strong></td>
                                    <td>{{ $employee->position }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employee Type:</strong></td>
                                    <td>
                                        {{ $employee->employeeType->EmployeeTypeName }}
                                        @if($employee->employeeType->hasBenefits)
                                            <span class="badge badge-success">Eligible for Benefits</span>
                                        @else
                                            <span class="badge badge-warning">Not Eligible for Benefits</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Salary:</strong></td>
                                    <td>
                                        @if($employee->monthly_salary)
                                            ₱{{ number_format($employee->monthly_salary, 2) }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($benefitCosts)
                                <h5>Benefit Cost Summary</h5>
                                <div class="alert alert-info">
                                    <strong>Total Benefit Cost:</strong> ₱{{ number_format($benefitCosts['total_cost'], 2) }}<br>
                                    <strong>Net Salary:</strong> ₱{{ number_format($benefitCosts['net_salary'], 2) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($employee->employeeType->hasBenefits)
                        <!-- Current Benefits -->
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Current Benefits</h5>
                                @if($employee->employeeBenefits->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Benefit Name</th>
                                                    <th>Type</th>
                                                    <th>Amount/Percentage</th>
                                                    <th>Effective Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->getCurrentBenefits() as $employeeBenefit)
                                                    <tr>
                                                        <td>{{ $employeeBenefit->benefit->BenefitName }}</td>
                                                        <td>
                                                            <span class="badge badge-info">{{ $employeeBenefit->benefit->BenefitType }}</span>
                                                        </td>
                                                        <td>
                                                            @if($employeeBenefit->benefit->BenefitType === 'Fixed')
                                                                ₱{{ number_format($employeeBenefit->Amount ?? $employeeBenefit->benefit->Amount, 2) }}
                                                            @elseif($employeeBenefit->benefit->BenefitType === 'Percentage')
                                                                {{ $employeeBenefit->Percentage ?? $employeeBenefit->benefit->Percentage }}%
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $employeeBenefit->EffectiveDate->format('M d, Y') }}</td>
                                                        <td>
                                                            @if($employeeBenefit->IsActive)
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($employeeBenefit->IsActive)
                                                                <form action="{{ route('employees.benefits.remove', [$employee, $employeeBenefit->BenefitID]) }}" 
                                                                      method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                                            onclick="return confirm('Are you sure you want to remove this benefit?')">
                                                                        <i class="fas fa-trash"></i> Remove
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No benefits assigned to this employee.
                                    </div>
                                @endif
                            </div>

                            <!-- Assign New Benefit -->
                            <div class="col-md-4">
                                <h5>Assign New Benefit</h5>
                                <form action="{{ route('employees.benefits.assign', $employee) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="benefit_id">Select Benefit:</label>
                                        <select name="benefit_id" id="benefit_id" class="form-control" required>
                                            <option value="">Choose a benefit...</option>
                                            @foreach($allBenefits as $benefit)
                                                @php
                                                    $hasBenefit = $employee->employeeBenefits()
                                                        ->where('BenefitID', $benefit->BenefitID)
                                                        ->where('IsActive', true)
                                                        ->exists();
                                                @endphp
                                                @if(!$hasBenefit)
                                                    <option value="{{ $benefit->BenefitID }}" 
                                                            data-type="{{ $benefit->BenefitType }}"
                                                            data-amount="{{ $benefit->Amount }}"
                                                            data-percentage="{{ $benefit->Percentage }}">
                                                        {{ $benefit->BenefitName }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="amount-group" style="display: none;">
                                        <label for="amount">Amount (₱):</label>
                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0">
                                    </div>

                                    <div class="form-group" id="percentage-group" style="display: none;">
                                        <label for="percentage">Percentage (%):</label>
                                        <input type="number" name="percentage" id="percentage" class="form-control" step="0.01" min="0" max="100">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Assign Benefit
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Benefit Cost Breakdown -->
                        @if($benefitCosts && count($benefitCosts['benefit_breakdown']) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Benefit Cost Breakdown</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Benefit</th>
                                                    <th>Type</th>
                                                    <th>Amount/Percentage</th>
                                                    <th>Monthly Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($benefitCosts['benefit_breakdown'] as $breakdown)
                                                    <tr>
                                                        <td>{{ $breakdown['benefit_name'] }}</td>
                                                        <td>{{ $breakdown['type'] }}</td>
                                                        <td>
                                                            @if($breakdown['type'] === 'Fixed')
                                                                ₱{{ number_format($breakdown['amount'], 2) }}
                                                            @elseif($breakdown['type'] === 'Percentage')
                                                                {{ $breakdown['percentage'] }}%
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>₱{{ number_format($breakdown['cost'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-primary">
                                                    <th colspan="3">Total Monthly Benefit Cost:</th>
                                                    <th>₱{{ number_format($benefitCosts['total_cost'], 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            This employee type ({{ $employee->employeeType->EmployeeTypeName }}) is not eligible for benefits.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const benefitSelect = document.getElementById('benefit_id');
    const amountGroup = document.getElementById('amount-group');
    const percentageGroup = document.getElementById('percentage-group');
    const amountInput = document.getElementById('amount');
    const percentageInput = document.getElementById('percentage');

    benefitSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const benefitType = selectedOption.getAttribute('data-type');
        const defaultAmount = selectedOption.getAttribute('data-amount');
        const defaultPercentage = selectedOption.getAttribute('data-percentage');

        // Hide all groups first
        amountGroup.style.display = 'none';
        percentageGroup.style.display = 'none';

        // Show relevant group based on benefit type
        if (benefitType === 'Fixed') {
            amountGroup.style.display = 'block';
            amountInput.value = defaultAmount || '';
        } else if (benefitType === 'Percentage') {
            percentageGroup.style.display = 'block';
            percentageInput.value = defaultPercentage || '';
        }
    });
});
</script>
@endsection
