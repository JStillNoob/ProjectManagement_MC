<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectStatus;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['StatusName' => 'Upcoming'],
            ['StatusName' => 'On Going'],
            ['StatusName' => 'Under Warranty'],
            ['StatusName' => 'Completed'],
            ['StatusName' => 'On Hold'],
            ['StatusName' => 'Cancelled'],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::create($status);
        }
    }
}
