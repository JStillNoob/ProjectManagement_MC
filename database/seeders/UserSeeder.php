<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'FirstName' => 'Admin',
            'MiddleName' => 'System',
            'LastName' => 'User',
            'Sex' => 'Male',
            'ContactNumber' => '09123456789',
            'Email' => 'admin@example.com',
            'Username' => 'admin',          // added
            'UserTypeID' => 1,                // e.g., 1 = Admin
            'Position' => 'System Admin',   // added
            'FlagDeleted' => 0,
            'email_verified_at' => now(),
            'Password' => Hash::make('password123'),
        ]);

        User::create([
            'FirstName' => 'Test',
            'MiddleName' => 'Sample',
            'LastName' => 'User',
            'Sex' => 'Female',
            'ContactNumber' => '09987654321',
            'Email' => 'test@example.com',
            'Username' => 'testuser',       // added
            'UserTypeID' => 4,                // e.g., 4 = Employee
            'Position' => 'Staff',
            'FlagDeleted' => 0,
            'email_verified_at' => now(),
            'Password' => Hash::make('12345678'),
        ]);
    }
}
