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
            'ContactNumber' => '09123456789',
            'Email' => 'admin@example.com',          // added
            'UserTypeID' => 2,                // e.g., 1 = Admin
            'FlagDeleted' => 0,
            'email_verified_at' => now(),
            'Password' => Hash::make('password123'),
        ]);

        
    }
}
