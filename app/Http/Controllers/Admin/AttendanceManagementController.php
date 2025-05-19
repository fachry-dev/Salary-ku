<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceManagementController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $selectedEmployeeId = $request->input('employee_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $query = Attendance::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc');

        if ($selectedEmployeeId) {
            $query->where('employee_id', $selectedEmployeeId);
        }

        $attendances = $query->paginate(15);

        return view('admin.attendance.index', compact('attendances', 'employees', 'selectedEmployeeId', 'month', 'year'));
    }

    // Optional: Form for admin to input/edit attendance if needed
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late,On Leave',
            'working_hours' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance already exists for this employee and date
        $existing = Attendance::where('employee_id', $request->employee_id)
                          ->where('date', $request->date)
                          ->first();
        if($existing) {
            return back()->withInput()->with('error', 'Attendance record for this employee on this date already exists.');
        }

        // Calculate working hours if both check-in and check-out are provided
        $workingHours = null;
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::createFromFormat('H:i', $request->check_in);
            $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
            $workingHours = $checkOut->diffInSeconds($checkIn) / 3600; // Convert seconds to hours
        }

        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'working_hours' => $workingHours ?? $request->working_hours,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record added successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::orderBy('name')->get();
        return view('admin.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s,H:i', // support H:i:s or H:i
            'check_out' => 'nullable|date_format:H:i:s,H:i|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late,On Leave',
            'working_hours' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Check if date is changed, whether attendance already exists for this employee and new date
        if ($request->date != $attendance->date->format('Y-m-d')) {
            $existing = Attendance::where('employee_id', $request->employee_id)
                              ->where('date', $request->date)
                              ->where('id', '!=', $attendance->id) // Ignore current record
                              ->first();
            if($existing) {
                return back()->withInput()->with('error', 'Attendance record for this employee on this date already exists.');
            }
        }

        // Calculate working hours if both check-in and check-out are provided
        $workingHours = null;
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::createFromFormat('H:i', $request->check_in);
            $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
            $workingHours = $checkOut->diffInSeconds($checkIn) / 3600; // Convert seconds to hours
        }

        $attendance->update([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'working_hours' => $workingHours ?? $request->working_hours,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted successfully.');
    }
}