<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Position;
use App\Models\ProjectEmployee;
use App\Models\EmployeeStatus;
use App\Models\Attendance;
use Carbon\Carbon;

class DavaoPortProjectEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Davao Port Logistics Warehouse project
        $project = Project::where('ProjectName', 'Davao Port Logistics Warehouse')->first();

        if (!$project) {
            $this->command->error('Project "Davao Port Logistics Warehouse" not found. Please create the project first.');
            return;
        }

        $this->command->info("Found project: {$project->ProjectName} (ID: {$project->ProjectID})");

        // Determine the assignment date - use project start date if available, otherwise use yesterday
        $assignmentDate = $project->StartDate ? $project->StartDate : now()->subDay()->toDateString();
        $this->command->info("Using assignment date: {$assignmentDate}");

        // Get position IDs for common construction positions
        $positions = [
            'Foreman' => Position::where('PositionName', 'Foreman')->first(),
            'Mason / Bricklayer' => Position::where('PositionName', 'Mason / Bricklayer')->first(),
            'Carpenter (Formworks / Rough Carpenter)' => Position::where('PositionName', 'Carpenter (Formworks / Rough Carpenter)')->first(),
            'Welder' => Position::where('PositionName', 'Welder')->first(),
            'Electrician' => Position::where('PositionName', 'Electrician')->first(),
            'Plumber' => Position::where('PositionName', 'Plumber')->first(),
            'Heavy Equipment Operator' => Position::where('PositionName', 'Heavy Equipment Operator')->first(),
            'Skilled Laborer' => Position::where('PositionName', 'Skilled Laborer')->first(),
        ];

        // Filter out null positions
        $positions = array_filter($positions);

        if (empty($positions)) {
            $this->command->error('No positions found. Please run PositionSeeder first.');
            return;
        }

        // Employees to create/assign for Davao Port Logistics Warehouse
        $employees = [
            [
                'first_name' => 'Reynaldo',
                'middle_name' => 'Santos',
                'last_name' => 'Torres',
                'birthday' => '1985-03-20',
                'contact_number' => '09171234501',
                'house_number' => 'B-12',
                'street' => 'Sampaguita Street',
                'barangay' => 'Buhangin',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-10',
                'position' => 'Mason / Bricklayer',
            ],
            [
                'first_name' => 'Roberto',
                'middle_name' => 'Cruz',
                'last_name' => 'Villanueva',
                'birthday' => '1987-07-15',
                'contact_number' => '09171234502',
                'house_number' => 'C-8',
                'street' => 'Narra Avenue',
                'barangay' => 'Tibungco',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-12',
                'position' => 'Carpenter (Formworks / Rough Carpenter)',
            ],
            [
                'first_name' => 'Edgar',
                'middle_name' => 'Mendoza',
                'last_name' => 'Ramos',
                'birthday' => '1989-11-08',
                'contact_number' => '09171234503',
                'house_number' => 'D-15',
                'street' => 'Acacia Road',
                'barangay' => 'Buhangin',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-15',
                'position' => 'Welder',
            ],
            [
                'first_name' => 'Fernando',
                'middle_name' => 'Garcia',
                'last_name' => 'Dela Cruz',
                'birthday' => '1986-02-14',
                'contact_number' => '09171234504',
                'house_number' => 'E-22',
                'street' => 'Mahogany Street',
                'barangay' => 'Tibungco',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-18',
                'position' => 'Electrician',
            ],
            [
                'first_name' => 'Armando',
                'middle_name' => 'Reyes',
                'last_name' => 'Bautista',
                'birthday' => '1988-09-25',
                'contact_number' => '09171234505',
                'house_number' => 'F-7',
                'street' => 'Molave Drive',
                'barangay' => 'Buhangin',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-20',
                'position' => 'Plumber',
            ],
            [
                'first_name' => 'Rodrigo',
                'middle_name' => 'Lopez',
                'last_name' => 'Sanchez',
                'birthday' => '1984-05-30',
                'contact_number' => '09171234506',
                'house_number' => 'G-18',
                'street' => 'Kamagong Avenue',
                'barangay' => 'Tibungco',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-22',
                'position' => 'Heavy Equipment Operator',
            ],
            [
                'first_name' => 'Carlos',
                'middle_name' => 'Morales',
                'last_name' => 'Fernandez',
                'birthday' => '1990-12-05',
                'contact_number' => '09171234507',
                'house_number' => 'H-9',
                'street' => 'Narra Street',
                'barangay' => 'Buhangin',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-25',
                'position' => 'Skilled Laborer',
            ],
            [
                'first_name' => 'Mario',
                'middle_name' => 'Castillo',
                'last_name' => 'Gutierrez',
                'birthday' => '1991-04-18',
                'contact_number' => '09171234508',
                'house_number' => 'I-14',
                'street' => 'Sampaguita Road',
                'barangay' => 'Tibungco',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-01-28',
                'position' => 'Skilled Laborer',
            ],
            [
                'first_name' => 'Ricardo',
                'middle_name' => 'Villanueva',
                'last_name' => 'Ocampo',
                'birthday' => '1987-08-22',
                'contact_number' => '09171234509',
                'house_number' => 'J-6',
                'street' => 'Acacia Avenue',
                'barangay' => 'Buhangin',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-02-01',
                'position' => 'Foreman',
            ],
            [
                'first_name' => 'Antonio',
                'middle_name' => 'Santos',
                'last_name' => 'Marquez',
                'birthday' => '1985-06-10',
                'contact_number' => '09171234510',
                'house_number' => 'K-11',
                'street' => 'Mahogany Drive',
                'barangay' => 'Tibungco',
                'city' => 'Davao City',
                'province' => 'Davao del Sur',
                'postal_code' => '8000',
                'start_date' => '2024-02-05',
                'position' => 'Mason / Bricklayer',
            ],
        ];

        $createdCount = 0;
        $assignedCount = 0;
        $skippedCount = 0;
        $attendanceCount = 0;

        foreach ($employees as $employeeData) {
            $positionName = $employeeData['position'];
            $position = $positions[$positionName] ?? null;

            if (!$position) {
                $this->command->warn("Position '{$positionName}' not found. Skipping employee: {$employeeData['first_name']} {$employeeData['last_name']}");
                $skippedCount++;
                continue;
            }

            // Check if employee already exists by name
            $existingEmployee = Employee::where('first_name', $employeeData['first_name'])
                ->where('last_name', $employeeData['last_name'])
                ->first();

            if ($existingEmployee) {
                $employee = $existingEmployee;
                // Update start_date to project start date if it's older
                if ($employee->start_date != $assignmentDate) {
                    $employee->start_date = $assignmentDate;
                    $employee->save();
                    $this->command->info("Updated start_date for existing employee: {$employee->full_name} to {$assignmentDate}");
                }
                $this->command->info("Using existing employee: {$employee->full_name}");
            } else {
                // Create new employee with start_date set to project start date
                $employee = Employee::create([
                    'first_name' => $employeeData['first_name'],
                    'middle_name' => $employeeData['middle_name'],
                    'last_name' => $employeeData['last_name'],
                    'birthday' => $employeeData['birthday'],
                    'contact_number' => $employeeData['contact_number'],
                    'house_number' => $employeeData['house_number'],
                    'street' => $employeeData['street'],
                    'barangay' => $employeeData['barangay'],
                    'city' => $employeeData['city'],
                    'province' => $employeeData['province'],
                    'postal_code' => $employeeData['postal_code'],
                    'PositionID' => $position->PositionID,
                    'start_date' => $assignmentDate, // Use project start date instead of hardcoded date
                    'employee_status_id' => EmployeeStatus::INACTIVE, // Will be set to Active when assigned
                ]);
                $createdCount++;
                $this->command->info("Created employee: {$employee->full_name} ({$positionName}) with start_date: {$assignmentDate}");
            }

            // Check if employee is already assigned to this project
            $existingAssignment = ProjectEmployee::where('ProjectID', $project->ProjectID)
                ->where('EmployeeID', $employee->id)
                ->first();

            if ($existingAssignment) {
                // Update the assigned_date to match project start date if it's different
                if ($existingAssignment->assigned_date != $assignmentDate) {
                    $existingAssignment->assigned_date = $assignmentDate;
                    $existingAssignment->save();
                    $this->command->info("Updated assigned_date for {$employee->full_name} to {$assignmentDate}");
                } else {
                    $this->command->info("Employee {$employee->full_name} is already assigned to this project with correct date. Skipping assignment.");
                }
                $skippedCount++;
                continue; // Skip creating new assignment, but attendance will be created for all assigned employees at the end
            }

            // Assign employee to project
            $projectEmployee = ProjectEmployee::create([
                'ProjectID' => $project->ProjectID,
                'EmployeeID' => $employee->id,
                'role_in_project' => null,
                'assigned_date' => $assignmentDate,
                'status' => 'Active'
            ]);

            // Ensure QR code is generated
            if (!$projectEmployee->qr_code) {
                $projectEmployee->generateQrCode();
            }

            $assignedCount++;
            $this->command->info("Assigned {$employee->full_name} to project: {$project->ProjectName}");
        }

        // Create attendance records for all assigned employees for yesterday (project start date)
        $this->command->info("\nCreating attendance records for yesterday ({$assignmentDate})...");
        $assignedEmployees = ProjectEmployee::where('ProjectID', $project->ProjectID)
            ->where('status', 'Active')
            ->with('employee')
            ->get();

        foreach ($assignedEmployees as $assignment) {
            $employee = $assignment->employee;
            if (!$employee) {
                continue;
            }

            // Check if attendance already exists for this date
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', $assignmentDate)
                ->first();

            if ($existingAttendance) {
                $this->command->info("Attendance already exists for {$employee->full_name} on {$assignmentDate}. Skipping.");
                continue;
            }

            // 85% chance of being present, 10% chance of being late, 5% chance of being absent
            $attendanceChance = rand(1, 100);
            $attendanceDate = Carbon::parse($assignmentDate);

            if ($attendanceChance <= 85) {
                // Present - on time or slightly late
                $timeIn = $this->generateTimeIn($attendanceDate);
                $timeOut = $this->generateTimeOut($attendanceDate, $timeIn);
                $status = $this->determineStatus($timeIn, $timeOut);

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $assignmentDate,
                    'time_in' => $timeIn->format('H:i:s'),
                    'time_out' => $timeOut->format('H:i:s'),
                    'status' => $status,
                    'remarks' => $this->getRandomRemarks($status),
                    'is_active' => true,
                ]);
                $attendanceCount++;
                $this->command->info("Created attendance for {$employee->full_name}: {$status} (Time In: {$timeIn->format('H:i A')}, Time Out: {$timeOut->format('H:i A')})");
            } elseif ($attendanceChance <= 95) {
                // Late
                $timeIn = $this->generateLateTimeIn($attendanceDate);
                $timeOut = $this->generateTimeOut($attendanceDate, $timeIn);

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $assignmentDate,
                    'time_in' => $timeIn->format('H:i:s'),
                    'time_out' => $timeOut->format('H:i:s'),
                    'status' => 'Late',
                    'remarks' => $this->getRandomRemarks('Late'),
                    'is_active' => true,
                ]);
                $attendanceCount++;
                $this->command->info("Created attendance for {$employee->full_name}: Late (Time In: {$timeIn->format('H:i A')}, Time Out: {$timeOut->format('H:i A')})");
            } else {
                // Absent
                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $assignmentDate,
                    'time_in' => null,
                    'time_out' => null,
                    'status' => 'Absent',
                    'remarks' => $this->getRandomRemarks('Absent'),
                    'is_active' => true,
                ]);
                $attendanceCount++;
                $this->command->info("Created attendance for {$employee->full_name}: Absent");
            }
        }

        $this->command->info("\nSeeder completed!");
        $this->command->info("  Created: {$createdCount} new employees");
        $this->command->info("  Assigned: {$assignedCount} employees to project");
        $this->command->info("  Skipped: {$skippedCount} employees (already exist or already assigned)");
        $this->command->info("  Attendance records created: {$attendanceCount}");
    }

    /**
     * Generate time in for attendance
     */
    private function generateTimeIn($date)
    {
        // 70% chance of being on time (8:00 AM), 30% chance of being slightly late
        if (rand(1, 100) <= 70) {
            // On time: 7:45 AM - 8:15 AM
            $hour = 8;
            $minute = rand(0, 15);
        } else {
            // Slightly late: 8:16 AM - 8:30 AM
            $hour = 8;
            $minute = rand(16, 30);
        }

        return $date->copy()->setTime($hour, $minute, 0);
    }

    /**
     * Generate late time in (after 8:30 AM)
     */
    private function generateLateTimeIn($date)
    {
        // Late: 8:31 AM - 9:30 AM
        $hour = 8;
        $minute = rand(31, 90);
        if ($minute >= 60) {
            $hour = 9;
            $minute = $minute - 60;
        }

        return $date->copy()->setTime($hour, $minute, 0);
    }

    /**
     * Generate time out for attendance
     */
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

    /**
     * Determine attendance status based on time in and time out
     */
    private function determineStatus($timeIn, $timeOut)
    {
        $timeInHour = $timeIn->hour;
        $timeInMinute = $timeIn->minute;

        // Check if late (after 8:30 AM)
        $isLate = ($timeInHour > 8) || ($timeInHour == 8 && $timeInMinute > 30);

        if ($isLate) {
            return 'Late';
        } else {
            return 'Present';
        }
    }

    /**
     * Get random remarks based on status
     */
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
            'Absent' => [
                'Sick leave',
                'Personal leave',
                'Family emergency',
                'Medical appointment',
                null,
            ]
        ];

        $statusRemarks = $remarks[$status] ?? [null];
        return $statusRemarks[array_rand($statusRemarks)];
    }
}

