<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Explicitly truncate users table first
        try {
            DB::statement('TRUNCATE TABLE `users`');
        } catch (\Exception $e) {
            DB::table('users')->delete();
        }
        
        // Get all table names - more reliable method
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $databaseName;
        
        // Tables to preserve
        $preserveTables = ['tblusertype'];
        
        // Truncate all tables except the ones to preserve
        foreach ($tables as $table) {
            // Get table name from the dynamic property
            $tableName = $table->$tableKey;
            
            if (!in_array($tableName, $preserveTables) && $tableName !== 'users') {
                try {
                    // Use raw SQL TRUNCATE for better reliability
                    DB::statement("TRUNCATE TABLE `{$tableName}`");
                } catch (\Exception $e) {
                    // If truncate fails, try delete instead
                    DB::table($tableName)->delete();
                }
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create test user
        \App\Models\User::create([
            'ContactNumber' => '09123456789',
            'Email' => 'admin@test.com',
            'Password' => 'password123',
            'UserTypeID' => 1, // Admin
            'FlagDeleted' => 0
        ]);
    }
}
