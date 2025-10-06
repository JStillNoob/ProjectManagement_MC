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
            ['EmployeeTypeName' => 'Regular'],
            ['EmployeeTypeName' => 'On-call'],
            ['EmployeeTypeName' => 'Contract'],
            ['EmployeeTypeName' => 'Part-time'],
        ];

        foreach ($employeeTypes as $type) {
            \App\Models\EmployeeType::create($type);
        }
    }
}
