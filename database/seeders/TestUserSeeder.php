<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'FirstName' => 'Admin',
            'MiddleName' => 'User',
            'LastName' => 'Test',
            'Sex' => 'Male',
            'ContactNumber' => '09123456789',
            'Email' => 'admin@test.com',
            'Username' => 'admin',
            'Password' => 'password123',
            'UserTypeID' => 1, // Admin
            'Position' => 'System Administrator',
            'FlagDeleted' => 0
        ]);
    }
}
