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
            ['UserType' => 'Engineer', 'FlagDeleted' => 0],        // UserTypeID 1 (formerly Production Head)
            ['UserType' => 'General Manager', 'FlagDeleted' => 0],              // UserTypeID 2
            ['UserType' => 'Foreman', 'FlagDeleted' => 0],          // UserTypeID 3 (formerly Attendant Officer)
            ['UserType' => 'Employee', 'FlagDeleted' => 0],         // UserTypeID 4
        ];

        foreach ($userTypes as $userType) {
            \App\Models\UserType::create($userType);
        }
    }
}
