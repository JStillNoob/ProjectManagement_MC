<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Position;
use App\Models\EmployeeStatus;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all position IDs
        $positionIds = Position::pluck('PositionID')->toArray();

        if (empty($positionIds)) {
            $this->command->error('No positions found. Please run PositionSeeder first.');
            return;
        }

        $employees = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'birthday' => '1990-05-15',
                'contact_number' => '09171234567',
                'house_number' => '123',
                'street' => 'Rizal Street',
                'barangay' => 'Barangay San Jose',
                'city' => 'Manila',
                'province' => 'Metro Manila',
                'postal_code' => '1000',
                'start_date' => '2023-01-15',
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Reyes',
                'last_name' => 'Garcia',
                'birthday' => '1988-08-22',
                'contact_number' => '09182345678',
                'house_number' => '456',
                'street' => 'Mabini Avenue',
                'barangay' => 'Barangay Poblacion',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'postal_code' => '1100',
                'start_date' => '2023-02-01',
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Lopez',
                'last_name' => 'Santos',
                'birthday' => '1985-03-10',
                'contact_number' => '09193456789',
                'house_number' => '789',
                'street' => 'Bonifacio Street',
                'barangay' => 'Barangay Bagumbayan',
                'city' => 'Makati',
                'province' => 'Metro Manila',
                'postal_code' => '1200',
                'start_date' => '2022-11-20',
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Mendoza',
                'last_name' => 'Fernandez',
                'birthday' => '1992-12-05',
                'contact_number' => '09204567890',
                'house_number' => '321',
                'street' => 'Luna Street',
                'barangay' => 'Barangay Sta. Cruz',
                'city' => 'Pasig',
                'province' => 'Metro Manila',
                'postal_code' => '1600',
                'start_date' => '2023-03-10',
            ],
            [
                'first_name' => 'Carlos',
                'middle_name' => 'Ramos',
                'last_name' => 'Villanueva',
                'birthday' => '1987-07-18',
                'contact_number' => '09215678901',
                'house_number' => '654',
                'street' => 'Aguinaldo Highway',
                'barangay' => 'Barangay Paliparan',
                'city' => 'Cavite City',
                'province' => 'Cavite',
                'postal_code' => '4100',
                'start_date' => '2022-09-05',
            ],
            [
                'first_name' => 'Rosa',
                'middle_name' => 'Cruz',
                'last_name' => 'Martinez',
                'birthday' => '1991-01-28',
                'contact_number' => '09226789012',
                'house_number' => '987',
                'street' => 'MacArthur Highway',
                'barangay' => 'Barangay Talaba',
                'city' => 'Bacoor',
                'province' => 'Cavite',
                'postal_code' => '4102',
                'start_date' => '2023-04-15',
            ],
            [
                'first_name' => 'Miguel',
                'middle_name' => 'Torres',
                'last_name' => 'Gonzales',
                'birthday' => '1989-09-12',
                'contact_number' => '09237890123',
                'house_number' => '147',
                'street' => 'Governor Drive',
                'barangay' => 'Barangay Poblacion',
                'city' => 'Dasmarinas',
                'province' => 'Cavite',
                'postal_code' => '4114',
                'start_date' => '2022-07-22',
            ],
            [
                'first_name' => 'Elena',
                'middle_name' => 'Bautista',
                'last_name' => 'Reyes',
                'birthday' => '1993-04-30',
                'contact_number' => '09248901234',
                'house_number' => '258',
                'street' => 'Molino Boulevard',
                'barangay' => 'Barangay Molino',
                'city' => 'Bacoor',
                'province' => 'Cavite',
                'postal_code' => '4102',
                'start_date' => '2023-05-01',
            ],
            [
                'first_name' => 'Roberto',
                'middle_name' => 'Castillo',
                'last_name' => 'Aquino',
                'birthday' => '1986-11-08',
                'contact_number' => '09259012345',
                'house_number' => '369',
                'street' => 'Taft Avenue',
                'barangay' => 'Barangay Malate',
                'city' => 'Manila',
                'province' => 'Metro Manila',
                'postal_code' => '1004',
                'start_date' => '2022-06-10',
            ],
            [
                'first_name' => 'Sofia',
                'middle_name' => 'Rivera',
                'last_name' => 'Pascual',
                'birthday' => '1994-06-25',
                'contact_number' => '09260123456',
                'house_number' => '741',
                'street' => 'España Boulevard',
                'barangay' => 'Barangay Sampaloc',
                'city' => 'Manila',
                'province' => 'Metro Manila',
                'postal_code' => '1008',
                'start_date' => '2023-06-20',
            ],
            [
                'first_name' => 'Antonio',
                'middle_name' => 'Navarro',
                'last_name' => 'Dizon',
                'birthday' => '1984-02-14',
                'contact_number' => '09271234567',
                'house_number' => '852',
                'street' => 'Ortigas Avenue',
                'barangay' => 'Barangay San Antonio',
                'city' => 'Pasig',
                'province' => 'Metro Manila',
                'postal_code' => '1605',
                'start_date' => '2022-04-18',
            ],
            [
                'first_name' => 'Isabella',
                'middle_name' => 'Ocampo',
                'last_name' => 'Soriano',
                'birthday' => '1990-10-03',
                'contact_number' => '09282345678',
                'house_number' => '963',
                'street' => 'Shaw Boulevard',
                'barangay' => 'Barangay Wack-Wack',
                'city' => 'Mandaluyong',
                'province' => 'Metro Manila',
                'postal_code' => '1550',
                'start_date' => '2023-07-05',
            ],
            [
                'first_name' => 'Francisco',
                'middle_name' => 'Zamora',
                'last_name' => 'Legaspi',
                'birthday' => '1988-05-20',
                'contact_number' => '09293456789',
                'house_number' => '174',
                'street' => 'C5 Road',
                'barangay' => 'Barangay Bagong Ilog',
                'city' => 'Pasig',
                'province' => 'Metro Manila',
                'postal_code' => '1600',
                'start_date' => '2022-08-12',
            ],
            [
                'first_name' => 'Carmen',
                'middle_name' => 'Delgado',
                'last_name' => 'Aguilar',
                'birthday' => '1995-08-17',
                'contact_number' => '09304567890',
                'house_number' => '285',
                'street' => 'EDSA',
                'barangay' => 'Barangay Guadalupe',
                'city' => 'Makati',
                'province' => 'Metro Manila',
                'postal_code' => '1212',
                'start_date' => '2023-08-01',
            ],
            [
                'first_name' => 'Rafael',
                'middle_name' => 'Enriquez',
                'last_name' => 'Tolentino',
                'birthday' => '1983-12-30',
                'contact_number' => '09315678901',
                'house_number' => '396',
                'street' => 'Commonwealth Avenue',
                'barangay' => 'Barangay Holy Spirit',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'postal_code' => '1127',
                'start_date' => '2022-03-25',
            ],
        ];

        foreach ($employees as $index => $employeeData) {
            // Assign a random position from available positions
            $employeeData['PositionID'] = $positionIds[array_rand($positionIds)];
            
            // Set status - mix of active and inactive
            $employeeData['employee_status_id'] = ($index < 10) ? EmployeeStatus::ACTIVE : EmployeeStatus::INACTIVE;
            
            // Set flag_deleted to false
            $employeeData['flag_deleted'] = false;

            Employee::updateOrCreate(
                [
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'birthday' => $employeeData['birthday'],
                ],
                $employeeData
            );
        }

        $this->command->info('15 employees seeded successfully!');
    }
}

