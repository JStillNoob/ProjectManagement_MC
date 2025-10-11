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
     * Display the attendance dashboard
     */
    public function index(Request $request)
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
        
        // Build query
        $query = Attendance::with('employee')
            ->where('attendance_date', $date)
            ->active();

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {
            $query->whereHas('employee', function($q) use ($department) {
                $q->where('position', 'like', '%' . $department . '%');
            });
        }

        $attendanceRecords = $query->orderBy('time_in', 'asc')->get();

        // Dashboard statistics
        $stats = $this->getDashboardStats($date);

        // Get all employees for comparison
        $allEmployees = Employee::active()->get();
        $presentEmployees = $attendanceRecords->where('status', 'Present')->pluck('employee_id')->toArray();
        $absentEmployees = $allEmployees->whereNotIn('id', $presentEmployees);

        return view('attendance.index', compact(
            'attendanceRecords', 
            'stats', 
            'date', 
            'status', 
            'department',
            'allEmployees',
            'absentEmployees'
        ));
    }

    /**
     * Get dashboard statistics for a specific date
     */
    private function getDashboardStats($date)
    {
        $totalEmployees = Employee::active()->count();
        $present = Attendance::where('attendance_date', $date)
            ->where('status', 'Present')
            ->count();
        $late = Attendance::where('attendance_date', $date)
            ->where('status', 'Late')
            ->count();
        $absent = $totalEmployees - $present;

        return [
            'total' => $totalEmployees,
            'present' => $present,
            'late' => $late,
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
            // Update existing record
            if ($action === 'time_in') {
                $attendance->update([
                    'time_in' => now()->format('H:i:s'),
                    'status' => $this->determineStatus($action, now()),
                    'remarks' => $remarks
                ]);
            } else {
                $attendance->update([
                    'time_out' => now()->format('H:i:s'),
                    'remarks' => $remarks
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'attendance' => $attendance->load('employee')
        ]);
    }

    /**
     * Determine attendance status based on time
     */
    private function determineStatus($action, $time)
    {
        if ($action === 'time_in') {
            // Check if employee is late (after 9:00 AM)
            $standardTime = Carbon::createFromTime(9, 0, 0);
            if ($time->format('H:i:s') > $standardTime->format('H:i:s')) {
                return 'Late';
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
}