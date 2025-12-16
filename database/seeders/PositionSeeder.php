<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Mason / Bricklayer',
            'Concrete Finisher',
            'Steelman / Rebar Worker',
            'Carpenter (Formworks / Rough Carpenter)',
            'Scaffolder',
            'Electrician',
            'Electrical Technician',
            'Plumber',
            'Pipefitter',
            'HVAC Technician',
            'Welder',
            'Tile Setter',
            'Painter',
            'Plasterer',
            'Ceiling Installer',
            'Glass Installer / Glazier',
            'Finishing Carpenter',
            'Heavy Equipment Operator',
            'Crane Operator',
            'Excavator Operator',
            'Backhoe Operator',
            'Rigger',
            'Foreman',
            'Leadman',
            'Skilled Laborer',
            'Helper / Assistant',
            'Fabricator',
        ];

        foreach ($positions as $positionName) {
            Position::updateOrCreate(
                ['PositionName' => $positionName]
            );
        }

        $this->command->info('Positions seeded successfully!');
    }
}

