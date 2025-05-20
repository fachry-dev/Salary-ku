<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    // Constant for deduction per absent day, could be placed in config
    const DEDUCTION_PER_ABSENCE = 50000; // Example

    public function index(Request $request)
    {
        $employees = Employee::orderBy('full_name')->get();
        $selectedEmployeeId = $request->input('employee_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $query = Payroll::with('employee')
                    ->where('month', $month)
                    ->where('year', $year)
                    ->orderBy('created_at', 'desc');

        if ($selectedEmployeeId) {
            $query->where('employee_id', $selectedEmployeeId);
        }
        $salaries = $query->paginate(10);

        return view('admin.salary.index', compact('salaries', 'employees', 'selectedEmployeeId', 'month', 'year'));
    }

    public function createForm(Request $request)
    {
        $employees = Employee::orderBy('full_name')->get();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        return view('admin.salary.create', compact('employees', 'month', 'year'));
    }

    public function calculateAndStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . (Carbon::now()->year + 5),
            'manual_deduction' => 'nullable|numeric|min:0' // Additional deduction if any
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $month = $request->month;
        $year = $request->year;

        // Check if salary for this period already exists
        $existingSalary = Payroll::where('employee_id', $employee->id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->first();
        if ($existingSalary) {
            return back()->with('error', 'Salary for this employee in this period has already been calculated.');
        }

        // Calculate attendance
        $attendanceData = Attendance::where('employee_id', $employee->id)
                            ->whereYear('date', $year)
                            ->whereMonth('date', $month)
                            ->selectRaw("
                                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as total_present,
                                SUM(CASE WHEN status IN ('leave', 'sick', 'absent') THEN 1 ELSE 0 END) as total_absent
                            ")
                            ->first();

        $totalPresent = $attendanceData->total_present ?? 0;
        $totalAbsent = $attendanceData->total_absent ?? 0;

        $baseSalary = $employee->base_salary;
        $attendanceDeduction = $totalAbsent * self::DEDUCTION_PER_ABSENCE;
        $manualDeduction = $request->input('manual_deduction', 0);
        $totalDeduction = $attendanceDeduction + $manualDeduction;
        $netSalary = $baseSalary - $totalDeduction;

        // Ensure net salary is not negative
        $netSalary = max(0, $netSalary);

        // Add try-catch to handle potential database errors
        try {
            Payroll::create([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'base_salary' => $baseSalary,
                'deduction' => $totalDeduction,
                'net_salary' => $netSalary,
                'salary_notes' => $request->input('salary_notes'),
                'payment_date' => $request->input('payment_date', Carbon::now())
            ]);
            
            return redirect()->route('admin.salary.index', ['month' => $month, 'year' => $year])
                            ->with('success', 'Salary for ' . $employee->full_name . ' has been calculated and saved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show(Payroll $gaji)
{
    $gaji->load('employee');
    return view('admin.salary.show', compact('gaji'));
}

public function edit(Payroll $gaji)
{
    $gaji->load('employee');
    $employees = Employee::orderBy('name')->get();
    return view('admin.salary.edit', compact('gaji', 'employees'));
}

    public function update(Request $request, Payroll $salary)
    {
        $request->validate([
            // employee_id, month, year should not be changed for existing salary records
            // if you want to change them, delete and create a new one
            'total_present' => 'required|integer|min:0',
            'total_absent' => 'required|integer|min:0',
            'base_salary' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'salary_notes' => 'nullable|string',
            'payment_date' => 'nullable|date'
        ]);

        // Recalculate Net Salary in case Base Salary or Deduction is manually changed in edit.
        $net_salary_recalc = $request->base_salary - $request->deduction;
        $net_salary_recalc = max(0, $net_salary_recalc);

        try {
            $salary->update([
                'total_present' => $request->total_present,
                'total_absent' => $request->total_absent,
                'base_salary' => $request->base_salary,
                'deduction' => $request->deduction,
                'net_salary' => $net_salary_recalc, // Use recalculated value
                'salary_notes' => $request->salary_notes,
                'payment_date' => $request->payment_date,
            ]);

            return redirect()->route('admin.salary.index', ['month' => $salary->month, 'year' => $salary->year])
                            ->with('success', 'Salary data has been updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Payroll $salary)
    {
        $month = $salary->month;
        $year = $salary->year;
        
        try {
            $salary->delete();
            return redirect()->route('admin.salary.index', ['month' => $month, 'year' => $year])
                            ->with('success', 'Salary data has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting: ' . $e->getMessage());
        }
    }

    // Function for "Print Simple Salary Slip"
    public function printSlip(Payroll $salary)
    {
        $salary->load('employee.user'); // Load employee and user relationships
        return view('admin.salary.slip', compact('salary'));
    }
}