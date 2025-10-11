<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Benefit;

class BenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = [
            [
                'BenefitName' => 'SSS (Social Security System)',
                'Description' => 'Social Security System contribution for retirement, disability, and death benefits',
                'Amount' => null,
                'Percentage' => 11.00, // Employee contribution
                'BenefitType' => 'Mandatory',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'PhilHealth',
                'Description' => 'Philippine Health Insurance Corporation for medical benefits',
                'Amount' => null,
                'Percentage' => 4.50, // Employee contribution
                'BenefitType' => 'Mandatory',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Pag-IBIG Fund',
                'Description' => 'Home Development Mutual Fund for housing and savings benefits',
                'Amount' => 100.00, // Fixed monthly contribution
                'Percentage' => null,
                'BenefitType' => 'Mandatory',
                'IsActive' => true,
            ],
            [
                'BenefitName' => '13th Month Pay',
                'Description' => 'Mandatory 13th month salary bonus',
                'Amount' => null,
                'Percentage' => 8.33, // 1/12 of annual salary
                'BenefitType' => 'Mandatory',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Health Insurance',
                'Description' => 'Company-provided health insurance coverage',
                'Amount' => 2000.00, // Monthly premium
                'Percentage' => null,
                'BenefitType' => 'Fixed',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Life Insurance',
                'Description' => 'Company-provided life insurance coverage',
                'Amount' => 500.00, // Monthly premium
                'Percentage' => null,
                'BenefitType' => 'Fixed',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Retirement Plan',
                'Description' => 'Company retirement savings plan',
                'Amount' => null,
                'Percentage' => 5.00, // Employee contribution
                'BenefitType' => 'Percentage',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Vacation Leave',
                'Description' => 'Paid vacation leave benefits',
                'Amount' => null,
                'Percentage' => null,
                'BenefitType' => 'Fixed',
                'IsActive' => true,
            ],
            [
                'BenefitName' => 'Sick Leave',
                'Description' => 'Paid sick leave benefits',
                'Amount' => null,
                'Percentage' => null,
                'BenefitType' => 'Fixed',
                'IsActive' => true,
            ],
        ];

        foreach ($benefits as $benefit) {
            Benefit::create($benefit);
        }
    }
}
