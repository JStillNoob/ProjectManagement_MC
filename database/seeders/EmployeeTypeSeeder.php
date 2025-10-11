<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeTypes = [
            ['EmployeeTypeName' => 'Regular', 'hasBenefits' => true],
            ['EmployeeTypeName' => 'On-call', 'hasBenefits' => false],
            ['EmployeeTypeName' => 'Contract', 'hasBenefits' => true],
            ['EmployeeTypeName' => 'Part-time', 'hasBenefits' => false],
        ];

        foreach ($employeeTypes as $type) {
            \App\Models\EmployeeType::create($type);
        }
    }
}
