<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            ['UserType' => 'Admin', 'FlagDeleted' => 0],
            ['UserType' => 'HR', 'FlagDeleted' => 0],
            ['UserType' => 'Manager', 'FlagDeleted' => 0],
            ['UserType' => 'Employee', 'FlagDeleted' => 0],
        ];

        foreach ($userTypes as $userType) {
            \App\Models\UserType::create($userType);
        }
    }
}
