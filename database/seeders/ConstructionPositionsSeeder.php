<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class ConstructionPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Mason / Bricklayer', 'salary' => 600],
            ['name' => 'Concrete Finisher', 'salary' => 600],
            ['name' => 'Steelman / Rebar Worker', 'salary' => 600],
            ['name' => 'Carpenter (Formworks / Rough Carpenter)', 'salary' => 600],
            ['name' => 'Scaffolder', 'salary' => 550],
            ['name' => 'Electrician', 'salary' => 700],
            ['name' => 'Electrical Technician', 'salary' => 700],
            ['name' => 'Plumber', 'salary' => 650],
            ['name' => 'Pipefitter', 'salary' => 650],
            ['name' => 'HVAC Technician', 'salary' => 800],
            ['name' => 'Welder', 'salary' => 650],
            ['name' => 'Tile Setter', 'salary' => 550],
            ['name' => 'Painter', 'salary' => 550],
            ['name' => 'Plasterer', 'salary' => 550],
            ['name' => 'Ceiling Installer', 'salary' => 550],
            ['name' => 'Glass Installer / Glazier', 'salary' => 550],
            ['name' => 'Finishing Carpenter', 'salary' => 600],
            ['name' => 'Heavy Equipment Operator', 'salary' => 800],
            ['name' => 'Crane Operator', 'salary' => 900],
            ['name' => 'Excavator Operator', 'salary' => 900],
            ['name' => 'Backhoe Operator', 'salary' => 800],
            ['name' => 'Rigger', 'salary' => 650],
            ['name' => 'Foreman', 'salary' => 900],
            ['name' => 'Leadman', 'salary' => 750],
            ['name' => 'Skilled Laborer', 'salary' => 550],
            ['name' => 'Helper / Assistant', 'salary' => 450],
            ['name' => 'Fabricator', 'salary' => 700],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['PositionName' => $position['name']],
                [
                    'Salary' => $position['salary'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Construction positions seeded successfully!');
    }
}