<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['RoleName' => 'HR'],
            ['RoleName' => 'Inventory'],
            ['RoleName' => 'Payroll'],
            ['RoleName' => 'Project Manager'],
            ['RoleName' => 'Admin'],
            ['RoleName' => 'General Manager'],
            ['RoleName' => 'Production Head'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
