<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'ClientName' => 'ABC Construction Corp.',
                'ContactPerson' => 'John Smith',
                'ContactNumber' => '+63 912 345 6789',
                'Email' => 'john.smith@abcconstruction.com',
            ],
            [
                'ClientName' => 'XYZ Development Inc.',
                'ContactPerson' => 'Maria Garcia',
                'ContactNumber' => '+63 917 123 4567',
                'Email' => 'maria.garcia@xyzdev.com',
            ],
            [
                'ClientName' => 'DEF Properties Ltd.',
                'ContactPerson' => 'Robert Johnson',
                'ContactNumber' => '+63 918 987 6543',
                'Email' => 'robert.johnson@defproperties.com',
            ],
        ];

        foreach ($clients as $client) {
            \App\Models\Client::create($client);
        }
    }
}