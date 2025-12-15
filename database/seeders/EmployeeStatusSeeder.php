<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'EmployeeStatusID' => 1,
                'StatusName' => 'Active',
                'Description' => 'Employee is currently assigned to a project',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'EmployeeStatusID' => 2,
                'StatusName' => 'Inactive',
                'Description' => 'Employee is not assigned to any project',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'EmployeeStatusID' => 3,
                'StatusName' => 'Archived',
                'Description' => 'Employee has been archived by admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('employee_status')->updateOrInsert(
                ['EmployeeStatusID' => $status['EmployeeStatusID']],
                $status
            );
        }
    }
}
