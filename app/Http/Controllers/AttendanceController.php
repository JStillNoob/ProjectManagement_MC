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
     * Mark attendance for an employee
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'action' => 'required|in:time_in,time_out',
            'remarks' => 'nullable|string|max:255'
        ]);

        $employeeId = $request->employee_id;
        $date = $request->attendance_date;
        $action = $request->action;
        $remarks = $request->remarks;

        // Check if attendance record exists for this date
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();

        if (!$attendance) {
            // Create new attendance record
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'attendance_date' => $date,
                'time_in' => $action === 'time_in' ? now()->format('H:i:s') : null,
                'time_out' => $action === 'time_out' ? now()->format('H:i:s') : null,
                'status' => $this->determineStatus($action, now()),
                'remarks' => $remarks
            ]);
        } else {
            // Check for duplicate actions
            if ($action === 'time_in' && !is_null($attendance->time_in)) {
                // Employee already clocked in today
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee has already clocked in today at ' . $attendance->time_in->format('H:i A')
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', 'Employee has already clocked in today at ' . $attendance->time_in->format('H:i A'));
            }
            
            if ($action === 'time_out' && !is_null($attendance->time_out)) {
                // Employee already clocked out today
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee has already clocked out today at ' . $attendance->time_out->format('H:i A')
                    ], 400);
                }
                return redirect()->route('attendance.index', ['date' => $date])
                    ->with('error', 'Employee has already clocked out today at ' . $attendance->time_out->format('H:i A'));
            }
            
            // Update existing record (only if not duplicate)
            if ($action === 'time_in') {
                $attendance->update([
                    'time_in' => now()->format('H:i:s'),
                    'status' => $this->determineStatus($action, now()),
                    'remarks' => $remarks
                ]);
            } else {
                $attendance->update([
                    'time_out' => now()->format('H:i:s'),
                    'status' => $this->determineStatus($action, now()),
                    'remarks' => $remarks
                ]);
            }
        }

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance->load('employee')
            ]);
        }
        
        // For form submission (like native PHP), redirect back with success message
        return redirect()->route('attendance.index', ['date' => $date])
            ->with('success', 'Attendance marked successfully!');
    }

    /**
     * Determine attendance status based on time
     */
    private function determineStatus($action, $time)
    {
        if ($action === 'time_in') {
            // Check if employee is late (after 8:30 AM)
            $standardTime = Carbon::createFromTime(8, 30, 0);
            if ($time->format('H:i:s') > $standardTime->format('H:i:s')) {
                return 'Late';
            }
            return 'Present';
        }
        
        if ($action === 'time_out') {
            // Check if employee worked overtime (after 5:30 PM)
            $standardEndTime = Carbon::createFromTime(17, 30, 0); // 5:30 PM
            if ($time->format('H:i:s') > $standardEndTime->format('H:i:s')) {
                return 'Overtime';
            }
            return 'Present';
        }
        
        return 'Present';
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
            fputcsv($file, ['Employee Name', 'Position', 'Time In', 'Time Out', 'Date', 'Status', 'Working Hours', 'Remarks']);
            
            // CSV data
            foreach ($attendanceRecords as $record) {
                fputcsv($file, [
                    $record->employee->full_name,
                    $record->employee->position ?? 'N/A',
                    $record->formatted_time_in,
                    $record->formatted_time_out,
                    $record->formatted_date,
                    $record->status,
                    $record->working_hours,
                    $record->remarks ?? ''
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
            'status' => 'required|in:Present,Absent,Late,Half Day',
            'remarks' => 'nullable|string|max:255'
        ]);

        $attendance = Attendance::findOrFail($id);
        
        $attendance->update([
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'status' => $request->status,
            'remarks' => $request->remarks
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
}