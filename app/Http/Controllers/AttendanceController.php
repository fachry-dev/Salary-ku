<?php

// app/Http/Controllers/AttendanceController.php
namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            // If user is an employee but employee data doesn't exist (shouldn't happen if flow is correct)
            Auth::logout();
            return redirect('/login')->with('error', 'Employee data not found. Please contact admin.');
        }

        $today = Carbon::today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                ->whereDate('date', $today)
                                ->first();

        $attendanceHistory = Attendance::where('employee_id', $employee->id)
                                ->orderBy('date', 'desc')
                                ->paginate(10); // Get 10 records per page

        return view('employee.dashboard', compact('todayAttendance', 'attendanceHistory', 'employee'));
    }

    public function clockIn(Request $request)
    {
        $employee = Auth::user()->employee;
        $today = Carbon::today();

        $existingAttendance = Attendance::where('employee_id', $employee->id)
                                ->whereDate('date', $today)
                                ->first();

        if ($existingAttendance && $existingAttendance->check_in) {
            return redirect()->route('employee.dashboard')->with('error', 'You have already checked in today.');
        }

        if ($existingAttendance) {
            $existingAttendance->update([
                'check_in' => Carbon::now()->format('H:i:s'),
                'status' => 'Present', // Assume clock in = present
            ]);
        } else {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in' => Carbon::now()->format('H:i:s'),
                'status' => 'Present',
                'working_hours' => null // Will be calculated on check out
            ]);
        }

        return redirect()->route('employee.dashboard')->with('success', 'Check-in recorded successfully.');
    }

    public function clockOut(Request $request)
    {
        $employee = Auth::user()->employee;
        $today = Carbon::today();

        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                ->whereDate('date', $today)
                                ->first();

        if (!$todayAttendance || !$todayAttendance->check_in) {
            return redirect()->route('employee.dashboard')->with('error', 'You have not checked in today.');
        }

        if ($todayAttendance->check_out) {
            return redirect()->route('employee.dashboard')->with('error', 'You have already checked out today.');
        }

        // Calculate working hours
        $checkIn = Carbon::parse($todayAttendance->check_in);
        $checkOut = Carbon::now();
        $workingHours = $checkOut->diffInSeconds($checkIn) / 3600; // Convert seconds to hours

        $todayAttendance->update([
            'check_out' => $checkOut->format('H:i:s'),
            'working_hours' => number_format($workingHours, 2)
        ]);

        return redirect()->route('employee.dashboard')->with('success', 'Check-out recorded successfully.');
    }

    // For admin to view all attendance records
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('employee.dashboard')->with('error', 'Unauthorized access.');
        }

        $today = Carbon::today();
        
        // Get attendance summary for today
        $presentCount = Attendance::whereDate('date', $today)
                                ->where('status', 'Present')
                                ->count();
        
        $absentCount = Attendance::whereDate('date', $today)
                                ->where('status', 'Absent')
                                ->count();
        
        $lateCount = Attendance::whereDate('date', $today)
                                ->where('status', 'Late')
                                ->count();
        
        $onLeaveCount = Attendance::whereDate('date', $today)
                                ->where('status', 'On Leave')
                                ->count();

        // Get all attendance records, paginated
        $attendanceRecords = Attendance::with('employee')
                                ->orderBy('date', 'desc')
                                ->paginate(15);

        return view('admin.attendance', compact(
            'attendanceRecords', 
            'presentCount', 
            'absentCount', 
            'lateCount', 
            'onLeaveCount'
        ));
    }
}