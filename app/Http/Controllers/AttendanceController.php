<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the attendance dashboard for a specific project
     */
    public function index(Request $request, $projectId = null)
    {
        // Check if user is Attendant Officer (UserTypeID = 3)
        if (Auth::user()->UserTypeID != 3) {
            abort(403, 'Access denied. Only Attendant Officers can access this page.');
        }
        
        $today = Carbon::today();
        
        // Get filter parameters
        $date = $request->get('date', $today->format('Y-m-d'));
        $status = $request->get('status');
        $department = $request->get('department');
        
        // Get project information
        $project = null;
        if ($projectId) {
            $project = \App\Models\Project::find($projectId);
            if (!$project) {
                abort(404, 'Project not found.');
            }
        } else {
            // Auto-detect user's assigned project
            $currentUser = Auth::user();
            if ($currentUser->EmployeeID) {
                $employee = Employee::find($currentUser->EmployeeID);
                if ($employee) {
                    // Get the first active project the employee is assigned to
                    $userProject = $employee->projects()
                        ->where('project_employees.status', 'Active')
                        ->first();
                    
                    if ($userProject) {
                        $project = $userProject;
                    }
                }
            }
        }
        
        // Build query - only show employees assigned to the current project
        $query = Attendance::with('employee.position')
            ->where('attendance_date', $date)
            ->active();

        // Filter by project if project is specified
        if ($project) {
            $query->whereHas('employee', function($q) use ($project) {
                $q->whereHas('projects', function($pq) use ($project) {
                    $pq->where('projects.ProjectID', $project->ProjectID)
                       ->where('project_employees.status', 'Active');
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {
            $query->whereHas('employee', function($q) use ($department) {
                $q->whereHas('position', function($positionQuery) use ($department) {
                    $positionQuery->where('PositionName', 'like', '%' . $department . '%');
                });
            });
        }

        $attendanceRecords = $query->orderBy('time_in', 'asc')->get();

        // Dashboard statistics
        $stats = $this->getDashboardStats($date, $project);

        // Get all employees for comparison - only project employees if project is specified
        if ($project) {
            $allEmployees = Employee::active()
                ->with('position')
                ->whereHas('projects', function($q) use ($project) {
                    $q->where('projects.ProjectID', $project->ProjectID)
                      ->where('project_employees.status', 'Active');
                })
                ->get();
        } else {
            $allEmployees = Employee::active()->with('position')->get();
        }
        
        $presentEmployees = $attendanceRecords->where('status', 'Present')->pluck('employee_id')->toArray();
        $absentEmployees = $allEmployees->whereNotIn('id', $presentEmployees);

        return view('attendance.index', compact(
            'attendanceRecords', 
            'stats', 
            'date', 
            'status', 
            'department',
            'allEmployees',
            'absentEmployees',
            'project'
        ));
    }

    /**
     * Get dashboard statistics for a specific date and project
     */
    private function getDashboardStats($date, $project = null)
    {
        // Get total employees - project-specific if project is specified
        if ($project) {
            $totalEmployees = Employee::active()
                ->whereHas('projects', function($q) use ($project) {
                    $q->where('projects.ProjectID', $project->ProjectID)
                      ->where('project_employees.status', 'Active');
                })
                ->count();
            
            $present = Attendance::where('attendance_date', $date)
                ->where('status', 'Present')
                ->whereHas('employee', function($q) use ($project) {
                    $q->whereHas('projects', function($pq) use ($project) {
                        $pq->where('projects.ProjectID', $project->ProjectID)
                           ->where('project_employees.status', 'Active');
                    });
                })
                ->count();
                
            $late = Attendance::where('attendance_date', $date)
                ->where('status', 'Late')
                ->whereHas('employee', function($q) use ($project) {
                    $q->whereHas('projects', function($pq) use ($project) {
                        $pq->where('projects.ProjectID', $project->ProjectID)
                           ->where('project_employees.status', 'Active');
                    });
                })
                ->count();
                
            $overtime = Attendance::where('attendance_date', $date)
                ->where('status', 'Overtime')
                ->whereHas('employee', function($q) use ($project) {
                    $q->whereHas('projects', function($pq) use ($project) {
                        $pq->where('projects.ProjectID', $project->ProjectID)
                           ->where('project_employees.status', 'Active');
                    });
                })
                ->count();
        } else {
            $totalEmployees = Employee::active()->count();
            $present = Attendance::where('attendance_date', $date)
                ->where('status', 'Present')
                ->count();
            $late = Attendance::where('attendance_date', $date)
                ->where('status', 'Late')
                ->count();
            $overtime = Attendance::where('attendance_date', $date)
                ->where('status', 'Overtime')
                ->count();
        }
        
        $absent = $totalEmployees - $present;

        return [
            'total' => $totalEmployees,
            'present' => $present,
            'late' => $late,
            'overtime' => $overtime,
            'absent' => $absent,
            'present_percentage' => $totalEmployees > 0 ? round(($present / $totalEmployees) * 100, 1) : 0
        ];
    }

    /**
     * Mark attendance for an employee (supports 4 actions: time_in, lunch_out, lunch_in, time_out)
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'action' => 'required|in:time_in,lunch_out,lunch_in,time_out'
        ]);

        $employeeId = $request->employee_id;
        $date = $request->attendance_date;
        $action = $request->action;
        $currentTime = now();
        $currentHour = $currentTime->hour;
        $currentMinute = $currentTime->minute;

        // Time-based validation rules
        // Rule 1: If it's 3 PM (15:00) or later, don't allow any clock in
        if ($currentHour >= 15 && ($action === 'time_in' || $action === 'lunch_in')) {
            $errorMessage = 'Clock in not allowed after 3:00 PM. You are too late for today.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            return redirect()->route('attendance.index', ['date' => $date])
                ->with('error', $errorMessage);
        }

        // Check if attendance record exists for this date
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();

        if (!$attendance) {
            // Rule 2: If it's 10 AM - 11:59 AM, don't allow morning time_in
            if ($action === 'time_in' && $currentHour >= 10 && $currentHour < 12) {
                $errorMessage = 'You are more than 2 hours late for morning shift (after 10:00 AM). Please come back for afternoon shift at 1:00 PM.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }

            // Rule 3: If it's during lunch period (12 PM - 12:59 PM), don't allow clock in
            if ($action === 'time_in' && $currentHour === 12) {
                $errorMessage = 'This is lunch break time (12:00 PM - 1:00 PM). Please clock in for afternoon shift at 1:00 PM.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }

            // Rule 4: If it's afternoon period (1 PM - 2:59 PM) and action is time_in, 
            // they should use lunch_in instead (afternoon time in)
            if ($action === 'time_in' && $currentHour >= 13 && $currentHour < 15) {
                // Create attendance with lunch_in (afternoon start) instead of time_in
                $attendance = Attendance::create([
                    'employee_id' => $employeeId,
                    'attendance_date' => $date,
                    'lunch_in' => $currentTime->format('H:i:s'),
                    'status' => 'Late' // They missed morning shift entirely
                ]);
                
                $successMessage = 'Afternoon shift started at ' . $currentTime->format('h:i A') . ' (Morning shift was missed)';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                        'attendance' => $attendance->load('employee'),
                        'next_action' => $attendance->getNextExpectedAction()
                    ]);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('success', $successMessage);
            }

            // Normal time_in for early arrivals (before 10 AM)
            if ($action !== 'time_in' && $action !== 'lunch_in') {
                $errorMessage = 'Employee must clock in first before ' . str_replace('_', ' ', $action);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }
            
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'attendance_date' => $date,
                'time_in' => $currentTime->format('H:i:s'),
                'status' => $this->determineStatus('time_in', $currentTime, null)
            ]);
        } else {
            // Apply time-based rules for existing records
            // Rule 2: If it's 10 AM - 11:59 AM and trying to do time_in, block it
            if ($action === 'time_in' && $currentHour >= 10 && $currentHour < 12) {
                $errorMessage = 'You are more than 2 hours late for morning shift (after 10:00 AM). Please come back for afternoon shift at 1:00 PM.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }

            // Rule 3: If it's during lunch period (12 PM - 12:59 PM) and trying to do time_in, block it
            if ($action === 'time_in' && $currentHour === 12) {
                $errorMessage = 'This is lunch break time (12:00 PM - 1:00 PM). Please clock in for afternoon shift at 1:00 PM.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }

            // Rule 4: If it's afternoon (1 PM - 2:59 PM) and trying to do time_in, 
            // redirect to lunch_in instead
            if ($action === 'time_in' && $currentHour >= 13 && $currentHour < 15) {
                $errorMessage = 'Morning shift has ended. Please use "Lunch In" to start your afternoon shift.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $errorMessage);
            }

            // Validate action sequence and check for duplicates
            $validationResult = $this->validateActionSequence($attendance, $action, $currentTime);
            if ($validationResult !== true) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validationResult
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', $validationResult);
            }
            
            // Update the appropriate field based on action
            $updateData = [];
            
            switch ($action) {
                case 'time_in':
                    $updateData['time_in'] = $currentTime->format('H:i:s');
                    $updateData['status'] = $this->determineStatus('time_in', $currentTime, null);
                    break;
                case 'lunch_out':
                    $updateData['lunch_out'] = $currentTime->format('H:i:s');
                    break;
                case 'lunch_in':
                    $updateData['lunch_in'] = $currentTime->format('H:i:s');
                    // If they're doing lunch_in and there's no time_in, mark as Late
                    if (is_null($attendance->time_in)) {
                        $updateData['status'] = 'Late';
                    }
                    break;
                case 'time_out':
                    $updateData['time_out'] = $currentTime->format('H:i:s');
                    $updateData['status'] = $this->determineStatus('time_out', $currentTime, $attendance);
                    break;
            }
            
            $attendance->update($updateData);
        }

        // Get action label for response message
        $actionLabels = [
            'time_in' => 'Time In',
            'lunch_out' => 'Lunch Out',
            'lunch_in' => 'Lunch In',
            'time_out' => 'Time Out'
        ];

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $actionLabels[$action] . ' recorded successfully',
                'attendance' => $attendance->load('employee'),
                'next_action' => $attendance->getNextExpectedAction()
            ]);
        }
        
        // For form submission (like native PHP), redirect back with success message
        return redirect()->route('attendance.index', ['date' => $date])
            ->with('success', $actionLabels[$action] . ' recorded successfully!');
    }

    /**
     * Validate the action sequence for attendance
     */
    private function validateActionSequence($attendance, $action, $time)
    {
        switch ($action) {
            case 'time_in':
                if (!is_null($attendance->time_in)) {
                    return 'Employee has already clocked in today at ' . $attendance->time_in->format('h:i A');
                }
                break;
            case 'lunch_out':
                if (is_null($attendance->time_in)) {
                    return 'Employee must clock in before taking lunch break';
                }
                if (!is_null($attendance->lunch_out)) {
                    return 'Employee has already clocked out for lunch at ' . $attendance->lunch_out->format('h:i A');
                }
                break;
            case 'lunch_in':
                // Allow lunch_in as first action for afternoon arrivals (no time_in required)
                // But check if they already have lunch_in
                if (!is_null($attendance->lunch_in)) {
                    return 'Employee has already clocked in from lunch at ' . $attendance->lunch_in->format('h:i A');
                }
                // If they have time_in and lunch_out is null, they should do lunch_out first
                if (!is_null($attendance->time_in) && is_null($attendance->lunch_out)) {
                    return 'Employee must clock out for lunch first';
                }
                break;
            case 'time_out':
                if (is_null($attendance->time_in)) {
                    return 'Employee must clock in before clocking out';
                }
                if (!is_null($attendance->time_out)) {
                    return 'Employee has already clocked out today at ' . $attendance->time_out->format('h:i A');
                }
                break;
        }
        return true;
    }

    /**
     * Determine attendance status based on time
     * 
     * Rules:
     * - Time In: Before 8:16 AM = Present, 8:16 AM+ = Late (15-min grace period)
     * - Time Out: Before 5:31 PM = keeps status, 5:31 PM - 9:00 PM = Overtime
     * - No time_out by 9:30 PM = Half Day
     */
    private function determineStatus($action, $time, $attendance = null)
    {
        if ($action === 'time_in') {
            // Check if employee is late (after 8:15 AM grace period)
            // 8:00 AM - 8:15 AM = On time, 8:16 AM+ = Late
            $graceEndTime = Carbon::createFromTime(8, 15, 0);
            if ($time->format('H:i:s') > $graceEndTime->format('H:i:s')) {
                return 'Late';
            }
            return 'Present';
        }
        
        if ($action === 'time_out') {
            // Get existing status (may be Late from time_in)
            $existingStatus = $attendance ? $attendance->status : 'Present';
            
            $overtimeStart = Carbon::createFromTime(17, 30, 0); // 5:30 PM
            $overtimeEnd = Carbon::createFromTime(21, 0, 0); // 9:00 PM
            $halfDayCutoff = Carbon::createFromTime(21, 30, 0); // 9:30 PM
            
            $timeOutStr = $time->format('H:i:s');
            
            // Check if employee worked overtime (5:31 PM - 9:00 PM)
            if ($timeOutStr > $overtimeStart->format('H:i:s') && $timeOutStr <= $overtimeEnd->format('H:i:s')) {
                return 'Overtime';
            }
            
            // If time out is after 9:30 PM, this shouldn't happen as it would be marked as Half Day
            // But we'll still handle it by returning Overtime (capped)
            if ($timeOutStr > $overtimeEnd->format('H:i:s')) {
                return 'Overtime';
            }
            
            // Normal checkout before overtime zone - keep existing status
            return $existingStatus;
        }
        
        return 'Present';
    }

    /**
     * Auto-determine the next expected action based on current time and attendance record
     */
    public function getExpectedAction(Request $request, $employeeId)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();
        
        $now = Carbon::now();
        $currentHour = $now->hour;
        
        if (!$attendance) {
            return response()->json([
                'expected_action' => 'time_in',
                'label' => 'Time In',
                'message' => 'Please clock in for today'
            ]);
        }
        
        $nextAction = $attendance->getNextExpectedAction();
        
        $actionLabels = [
            'time_in' => 'Time In',
            'lunch_out' => 'Lunch Out',
            'lunch_in' => 'Lunch In',
            'time_out' => 'Time Out',
            'complete' => 'Complete'
        ];
        
        $actionMessages = [
            'time_in' => 'Please clock in for today',
            'lunch_out' => 'Clock out for lunch break',
            'lunch_in' => 'Clock in from lunch break',
            'time_out' => 'Clock out for the day',
            'complete' => 'All attendance recorded for today'
        ];
        
        return response()->json([
            'expected_action' => $nextAction,
            'label' => $actionLabels[$nextAction],
            'message' => $actionMessages[$nextAction],
            'attendance' => $attendance
        ]);
    }

    /**
     * Export attendance data to CSV
     */
    public function exportCsv(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $status = $request->get('status');
        
        $query = Attendance::with('employee')
            ->where('attendance_date', $date)
            ->active();

        if ($status) {
            $query->where('status', $status);
        }

        $attendanceRecords = $query->orderBy('time_in', 'asc')->get();

        $filename = 'attendance_' . $date . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendanceRecords) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Employee Name', 'Position', 'Time In', 'Time Out', 'Date', 'Status', 'Working Hours']);
            
            // CSV data
            foreach ($attendanceRecords as $record) {
                fputcsv($file, [
                    $record->employee->full_name,
                    $record->employee->position ?? 'N/A',
                    $record->formatted_time_in,
                    $record->formatted_time_out,
                    $record->formatted_date,
                    $record->status,
                    $record->working_hours
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get attendance history for an employee
     */
    public function getEmployeeHistory(Request $request, $employeeId)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $attendanceHistory = Attendance::with('employee')
            ->where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->get();

        return response()->json($attendanceHistory);
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Half Day'
        ]);

        $attendance = Attendance::findOrFail($id);
        
        $attendance->update([
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully'
        ]);
    }

    /**
     * Delete attendance record
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully'
        ]);
    }

    /**
     * Show QR codes for all employees
     */
    public function showQrCodes()
    {
        $employees = Employee::active()->with('position')->get();
        
        return view('attendance.qr-codes', compact('employees'));
    }

    /**
     * Show attendance overview for Production Head
     */
    public function prodHeadOverview(Request $request)
    {
        // Check if user is Production Head (UserTypeID = 1) or HR Admin (UserTypeID = 2)
        $user = Auth::user();
        $userTypeId = $user->getUserTypeId();
        
        if (!in_array($userTypeId, [1, 2])) {
            abort(403, 'Access denied. Only Production Heads and HR Admins can access this page.');
        }
        
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get all active projects
        $projects = \App\Models\Project::active()->get();
        
        // Get attendance data grouped by project
        $projectAttendanceData = [];
        $overallSummary = [
            'total_employees' => 0,
            'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
            'total_attendance_records' => 0,
            'present_count' => 0,
            'late_count' => 0,
            'overtime_count' => 0,
            'half_day_count' => 0,
            'absent_count' => 0,
            'attendance_rate' => 0
        ];
        
        foreach ($projects as $project) {
            // Get employees assigned to this project
            $projectEmployees = $project->employees()
                ->where('project_employees.status', 'Active')
                ->get();
            
            if ($projectEmployees->isEmpty()) {
                continue; // Skip projects with no active employees
            }
            
            // Get attendance records for this project
            $projectAttendance = Attendance::with('employee.position')
                ->whereHas('employee.projects', function($q) use ($project) {
                    $q->where('projects.ProjectID', $project->ProjectID)
                      ->where('project_employees.status', 'Active');
                })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date', 'desc')
                ->orderBy('employee_id')
                ->get();
            
            // Calculate project-specific summary
            $projectSummary = $this->calculateProjectAttendanceSummary($projectAttendance, $projectEmployees, $startDate, $endDate);
            
            // Add to project data
            $projectAttendanceData[] = [
                'project' => $project,
                'employees' => $projectEmployees,
                'attendance' => $projectAttendance,
                'summary' => $projectSummary
            ];
            
            // Add to overall summary
            $overallSummary['total_employees'] += $projectEmployees->count();
            $overallSummary['total_attendance_records'] += $projectAttendance->count();
            $overallSummary['present_count'] += $projectSummary['present_count'];
            $overallSummary['late_count'] += $projectSummary['late_count'];
            $overallSummary['overtime_count'] += $projectSummary['overtime_count'];
            $overallSummary['half_day_count'] += $projectSummary['half_day_count'];
            $overallSummary['absent_count'] += $projectSummary['absent_count'];
        }
        
        // Calculate overall attendance rate
        $totalPossibleAttendance = $overallSummary['total_employees'] * $overallSummary['total_days'];
        if ($totalPossibleAttendance > 0) {
            $overallSummary['attendance_rate'] = round(($overallSummary['total_attendance_records'] / $totalPossibleAttendance) * 100, 1);
        }
        
        return view('ProdHeadPage.attendance-overview', compact(
            'projectAttendanceData', 
            'projects', 
            'startDate', 
            'endDate',
            'overallSummary'
        ));
    }
    
    /**
     * Calculate project-specific attendance summary statistics
     */
    private function calculateProjectAttendanceSummary($attendanceRecords, $projectEmployees, $startDate, $endDate)
    {
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        
        $summary = [
            'total_employees' => $projectEmployees->count(),
            'total_days' => $totalDays,
            'total_attendance_records' => $attendanceRecords->count(),
            'present_count' => $attendanceRecords->where('status', 'Present')->count(),
            'late_count' => $attendanceRecords->where('status', 'Late')->count(),
            'overtime_count' => $attendanceRecords->where('status', 'Overtime')->count(),
            'half_day_count' => $attendanceRecords->where('status', 'Half Day')->count(),
            'absent_count' => 0,
            'attendance_rate' => 0
        ];
        
        // Calculate absent days for each employee in this project
        foreach ($projectEmployees as $employee) {
            $employeeAttendance = $attendanceRecords->where('employee_id', $employee->id);
            $attendedDays = $employeeAttendance->count();
            $absentDays = $totalDays - $attendedDays;
            $summary['absent_count'] += $absentDays;
        }
        
        // Calculate attendance rate for this project
        $totalPossibleAttendance = $projectEmployees->count() * $totalDays;
        if ($totalPossibleAttendance > 0) {
            $summary['attendance_rate'] = round(($summary['total_attendance_records'] / $totalPossibleAttendance) * 100, 1);
        }
        
        return $summary;
    }

    /**
     * Calculate attendance summary statistics (legacy method)
     */
    private function calculateAttendanceSummary($attendanceRecords, $startDate, $endDate, $projectId = null)
    {
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        
        // Get all employees for the selected project/period
        $employeeQuery = Employee::active();
        if ($projectId) {
            $employeeQuery->whereHas('projects', function($q) use ($projectId) {
                $q->where('projects.ProjectID', $projectId)
                  ->where('project_employees.status', 'Active');
            });
        }
        $allEmployees = $employeeQuery->get();
        
        $summary = [
            'total_employees' => $allEmployees->count(),
            'total_days' => $totalDays,
            'total_attendance_records' => $attendanceRecords->count(),
            'present_count' => $attendanceRecords->where('status', 'Present')->count(),
            'late_count' => $attendanceRecords->where('status', 'Late')->count(),
            'overtime_count' => $attendanceRecords->where('status', 'Overtime')->count(),
            'absent_count' => 0, // Will be calculated below
            'attendance_rate' => 0 // Will be calculated below
        ];
        
        // Calculate absent days for each employee
        $employeeAbsentDays = [];
        foreach ($allEmployees as $employee) {
            $employeeAttendance = $attendanceRecords->where('employee_id', $employee->id);
            $attendedDays = $employeeAttendance->count();
            $absentDays = $totalDays - $attendedDays;
            $employeeAbsentDays[$employee->id] = $absentDays;
            $summary['absent_count'] += $absentDays;
        }
        
        // Calculate attendance rate
        $totalPossibleAttendance = $allEmployees->count() * $totalDays;
        if ($totalPossibleAttendance > 0) {
            $summary['attendance_rate'] = round(($summary['total_attendance_records'] / $totalPossibleAttendance) * 100, 1);
        }
        
        return $summary;
    }
    
    /**
     * Export attendance data to PDF for Production Head
     */
    public function exportAttendancePdf(Request $request)
    {
        // Check if user is Production Head (UserTypeID = 1) or HR Admin (UserTypeID = 2)
        $user = Auth::user();
        $userTypeId = $user->getUserTypeId();
        
        if (!in_array($userTypeId, [1, 2])) {
            abort(403, 'Access denied. Only Production Heads and HR Admins can access this page.');
        }
        
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get all active projects
        $projects = \App\Models\Project::active()->get();
        
        // Get attendance data grouped by project (same logic as overview)
        $projectAttendanceData = [];
        $overallSummary = [
            'total_employees' => 0,
            'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
            'total_attendance_records' => 0,
            'present_count' => 0,
            'late_count' => 0,
            'overtime_count' => 0,
            'absent_count' => 0,
            'attendance_rate' => 0
        ];
        
        foreach ($projects as $project) {
            // Get employees assigned to this project
            $projectEmployees = $project->employees()
                ->where('project_employees.status', 'Active')
                ->get();
            
            if ($projectEmployees->isEmpty()) {
                continue; // Skip projects with no active employees
            }
            
            // Get attendance records for this project
            $projectAttendance = Attendance::with('employee.position')
                ->whereHas('employee.projects', function($q) use ($project) {
                    $q->where('projects.ProjectID', $project->ProjectID)
                      ->where('project_employees.status', 'Active');
                })
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->orderBy('attendance_date', 'desc')
                ->orderBy('employee_id')
                ->get();
            
            // Calculate project-specific summary
            $projectSummary = $this->calculateProjectAttendanceSummary($projectAttendance, $projectEmployees, $startDate, $endDate);
            
            // Add to project data
            $projectAttendanceData[] = [
                'project' => $project,
                'employees' => $projectEmployees,
                'attendance' => $projectAttendance,
                'summary' => $projectSummary
            ];
            
            // Add to overall summary
            $overallSummary['total_employees'] += $projectEmployees->count();
            $overallSummary['total_attendance_records'] += $projectAttendance->count();
            $overallSummary['present_count'] += $projectSummary['present_count'];
            $overallSummary['late_count'] += $projectSummary['late_count'];
            $overallSummary['overtime_count'] += $projectSummary['overtime_count'];
            $overallSummary['absent_count'] += $projectSummary['absent_count'];
        }
        
        // Calculate overall attendance rate
        $totalPossibleAttendance = $overallSummary['total_employees'] * $overallSummary['total_days'];
        if ($totalPossibleAttendance > 0) {
            $overallSummary['attendance_rate'] = round(($overallSummary['total_attendance_records'] / $totalPossibleAttendance) * 100, 1);
        }
        
        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ProdHeadPage.attendance-pdf', compact(
            'projectAttendanceData', 
            'startDate', 
            'endDate', 
            'overallSummary'
        ));
        
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);
        
        return $pdf->download('attendance_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    /**
     * Generate QR code for a specific employee
     */
    public function generateQrCode($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        
        // Generate QR code data
        $qrData = $employee->qr_code_data;
        
        // For now, we'll return the data as JSON
        // In a real implementation, you might want to generate an actual QR code image
        return response()->json([
            'success' => true,
            'employee' => $employee,
            'qr_data' => $qrData,
            'qr_url' => $employee->generateQrCodeUrl()
        ]);
    }

    /**
     * Get employee attendance details for admin modal view
     */
    public function getEmployeeAttendanceDetails(Request $request, $employeeId)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $employee = Employee::with('position')->findOrFail($employeeId);
        
        $attendanceRecords = Attendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->get();
        
        // Calculate summary statistics
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $presentDays = $attendanceRecords->whereIn('status', ['Present', 'Overtime'])->count();
        $lateDays = $attendanceRecords->where('status', 'Late')->count();
        $overtimeDays = $attendanceRecords->where('status', 'Overtime')->count();
        $halfDays = $attendanceRecords->where('status', 'Half Day')->count();
        $absentDays = $totalDays - $attendanceRecords->count();
        
        // Format records for response
        $formattedRecords = $attendanceRecords->map(function($record) {
            return [
                'id' => $record->id,
                'date' => $record->attendance_date->format('Y-m-d'),
                'formatted_date' => $record->formatted_date,
                'time_in' => $record->formatted_time_in,
                'lunch_out' => $record->formatted_lunch_out,
                'lunch_in' => $record->formatted_lunch_in,
                'time_out' => $record->formatted_time_out,
                'status' => $record->status,
                'working_hours' => $record->working_hours,
                'overtime_hours' => $record->overtime_hours
            ];
        });
        
        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'position' => $employee->position ? $employee->position->PositionName : 'N/A'
            ],
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'overtime_days' => $overtimeDays,
                'half_days' => $halfDays,
                'absent_days' => $absentDays,
                'attendance_rate' => $totalDays > 0 ? round(($attendanceRecords->count() / $totalDays) * 100, 1) : 0
            ],
            'records' => $formattedRecords
        ]);
    }

    /**
     * Mark employees without time_out as Half Day (scheduled task or manual trigger)
     */
    public function markHalfDayForMissingTimeOut()
    {
        $today = Carbon::today();
        $halfDayCutoff = Carbon::createFromTime(21, 30, 0); // 9:30 PM
        
        // Only run if it's past 9:30 PM
        if (Carbon::now()->format('H:i:s') < $halfDayCutoff->format('H:i:s')) {
            return response()->json([
                'success' => false,
                'message' => 'This action can only be performed after 9:30 PM'
            ], 400);
        }
        
        // Find all attendance records from today that have time_in but no time_out
        $affectedRecords = Attendance::where('attendance_date', $today)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->update(['status' => 'Half Day']);
        
        return response()->json([
            'success' => true,
            'message' => $affectedRecords . ' records marked as Half Day',
            'affected_count' => $affectedRecords
        ]);
    }
}