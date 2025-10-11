<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed project statuses and new tables
        $this->call([
            ProjectStatusSeeder::class,
            EmployeeTypeSeeder::class,
            BenefitSeeder::class,
            RoleSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
