<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Position;
use App\Models\ProjectEmployee;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeProjectAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all projects
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->command->error('No projects found. Please create projects first.');
            return;
        }

        // Get all positions
        $positions = Position::where('is_active', true)->get();
        
        if ($positions->isEmpty()) {
            $this->command->error('No positions found. Please run ConstructionPositionsSeeder first.');
            return;
        }

        // Create employees for each project
        $employees = [];
        $employeeCounter = 1;

        foreach ($projects as $project) {
            $this->command->info("Creating employees for project: {$project->ProjectName}");
            
            // Create 3-5 employees per project
            $employeeCount = rand(3, 5);
            
            for ($i = 0; $i < $employeeCount; $i++) {
                $position = $positions->random();
                
                $employee = Employee::create([
                    'first_name' => $this->getRandomFirstName(),
                    'last_name' => $this->getRandomLastName(),
                    'birthday' => Carbon::now()->subYears(rand(25, 55))->subDays(rand(1, 365)),
                    'age' => rand(25, 55),
                    'contact_number' => $this->getRandomPhone(),
                    'house_number' => rand(100, 9999),
                    'street' => $this->getRandomStreet(),
                    'barangay' => $this->getRandomBarangay(),
                    'city' => $this->getRandomCity(),
                    'province' => 'Metro Manila',
                    'postal_code' => rand(1000, 9999),
                    'country' => 'Philippines',
                    'status' => 'Active',
                    'PositionID' => $position->PositionID,
                    'base_salary' => $position->Salary,
                    'start_date' => Carbon::now()->subMonths(rand(1, 12)),
                    'flag_deleted' => false,
                    'EmployeeTypeID' => 1, // Regular employee
                ]);

                // Assign employee to project
                ProjectEmployee::create([
                    'ProjectID' => $project->ProjectID,
                    'EmployeeID' => $employee->id,
                    'role_in_project' => $position->PositionName,
                    'assigned_date' => Carbon::now()->subDays(rand(1, 30)),
                    'status' => 'Active',
                ]);

                $employees[] = [
                    'employee' => $employee,
                    'project' => $project,
                    'position' => $position
                ];

                $employeeCounter++;
            }
        }

        $this->command->info("Created " . count($employees) . " employees across all projects.");

        // Generate attendance data for October 6-10
        $this->generateAttendanceData($employees);

        $this->command->info('Employee project attendance data seeded successfully!');
    }

    private function generateAttendanceData($employees)
    {
        $this->command->info('Generating attendance data for October 6-10...');
        
        $dates = [
            Carbon::parse('2024-10-06'), // Sunday
            Carbon::parse('2024-10-07'), // Monday
            Carbon::parse('2024-10-08'), // Tuesday
            Carbon::parse('2024-10-09'), // Wednesday
            Carbon::parse('2024-10-10'), // Thursday
        ];

        $attendanceCount = 0;

        foreach ($employees as $employeeData) {
            $employee = $employeeData['employee'];
            $project = $employeeData['project'];

            foreach ($dates as $date) {
                // Skip Sunday (weekend)
                if ($date->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                // 90% chance of attendance (10% chance of being absent)
                if (rand(1, 100) <= 90) {
                    $timeIn = $this->generateTimeIn($date);
                    $timeOut = $this->generateTimeOut($date, $timeIn);
                    $status = $this->determineStatus($timeIn, $timeOut);

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->format('Y-m-d'),
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'status' => $status,
                        'remarks' => $this->getRandomRemarks($status),
                        'is_active' => true,
                    ]);

                    $attendanceCount++;
                }
            }
        }

        $this->command->info("Generated {$attendanceCount} attendance records.");
    }

    private function generateTimeIn($date)
    {
        // 70% chance of being on time (8:00 AM), 30% chance of being late
        if (rand(1, 100) <= 70) {
            // On time: 7:45 AM - 8:15 AM
            $hour = 8;
            $minute = rand(0, 15);
        } else {
            // Late: 8:16 AM - 9:30 AM
            $hour = 8;
            $minute = rand(16, 90);
            if ($minute >= 60) {
                $hour = 9;
                $minute = $minute - 60;
            }
        }

        return $date->copy()->setTime($hour, $minute, 0);
    }

    private function generateTimeOut($date, $timeIn)
    {
        // 80% chance of normal time out (5:00 PM), 20% chance of overtime
        if (rand(1, 100) <= 80) {
            // Normal: 4:45 PM - 5:15 PM
            $hour = 17;
            $minute = rand(0, 15);
        } else {
            // Overtime: 5:16 PM - 7:00 PM
            $hour = 17;
            $minute = rand(16, 120);
            if ($minute >= 60) {
                $hour = 18;
                $minute = $minute - 60;
            }
        }

        return $date->copy()->setTime($hour, $minute, 0);
    }

    private function determineStatus($timeIn, $timeOut)
    {
        $timeInHour = $timeIn->hour;
        $timeInMinute = $timeIn->minute;
        $timeOutHour = $timeOut->hour;
        $timeOutMinute = $timeOut->minute;

        // Check if late (after 8:30 AM)
        $isLate = ($timeInHour > 8) || ($timeInHour == 8 && $timeInMinute > 30);
        
        // Check if overtime (after 5:30 PM)
        $isOvertime = ($timeOutHour > 17) || ($timeOutHour == 17 && $timeOutMinute > 30);

        if ($isOvertime) {
            return 'Overtime';
        } elseif ($isLate) {
            return 'Late';
        } else {
            return 'Present';
        }
    }

    private function getRandomRemarks($status)
    {
        $remarks = [
            'Present' => [
                'On time today',
                'Good attendance',
                'Regular work day',
                'Completed tasks',
                null, // 20% chance of no remarks
                null,
            ],
            'Late' => [
                'Traffic jam',
                'Personal emergency',
                'Transportation delay',
                'Overslept',
                'Family matter',
                null,
            ],
            'Overtime' => [
                'Project deadline',
                'Extra work required',
                'Client meeting',
                'System maintenance',
                'Quality check',
                null,
            ]
        ];

        $statusRemarks = $remarks[$status] ?? [null];
        return $statusRemarks[array_rand($statusRemarks)];
    }

    private function getRandomFirstName()
    {
        $firstNames = [
            'Juan', 'Pedro', 'Miguel', 'Carlos', 'Jose', 'Antonio', 'Francisco', 'Manuel',
            'David', 'Rafael', 'Jorge', 'Luis', 'Fernando', 'Roberto', 'Eduardo', 'Sergio',
            'Andres', 'Ricardo', 'Alberto', 'Mario', 'Diego', 'Alejandro', 'Gabriel', 'Daniel',
            'Maria', 'Carmen', 'Ana', 'Isabel', 'Rosa', 'Elena', 'Pilar', 'Dolores',
            'Teresa', 'Cristina', 'Monica', 'Patricia', 'Laura', 'Sandra', 'Beatriz', 'Natalia'
        ];
        
        return $firstNames[array_rand($firstNames)];
    }

    private function getRandomLastName()
    {
        $lastNames = [
            'Garcia', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Perez', 'Sanchez',
            'Ramirez', 'Cruz', 'Flores', 'Gomez', 'Diaz', 'Reyes', 'Morales', 'Jimenez',
            'Ruiz', 'Torres', 'Mendoza', 'Vargas', 'Castillo', 'Romero', 'Herrera', 'Medina',
            'Aguilar', 'Moreno', 'Munoz', 'Alvarez', 'Rivera', 'Ramos', 'Ortega', 'Silva'
        ];
        
        return $lastNames[array_rand($lastNames)];
    }

    private function getRandomPhone()
    {
        return '+63' . rand(900, 999) . rand(1000000, 9999999);
    }

    private function getRandomStreet()
    {
        $streets = ['Main St', 'Oak Ave', 'Pine Rd', 'Cedar Ln', 'Elm St', 'Maple Dr', 'First St', 'Second Ave'];
        return $streets[array_rand($streets)];
    }

    private function getRandomBarangay()
    {
        $barangays = ['Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5', 'Barangay 6', 'Barangay 7', 'Barangay 8'];
        return $barangays[array_rand($barangays)];
    }

    private function getRandomCity()
    {
        $cities = ['Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Marikina', 'Mandaluyong', 'San Juan'];
        return $cities[array_rand($cities)];
    }
}