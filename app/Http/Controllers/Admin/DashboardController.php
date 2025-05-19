<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->format('F');
        $currentYear = Carbon::now()->year;
        
        // Get today's date
        $today = Carbon::today();
        
        // Dashboard statistics
        $stats = [
            'totalEmployees' => Employee::count(),
            'totalPayroll' => Payroll::where('month', $currentMonth)
                                ->where('year', $currentYear)
                                ->sum('net_salary'),
            'employeesPaid' => Payroll::where('month', $currentMonth)
                                ->where('year', $currentYear)
                                ->where('status', 'Paid')
                                ->count(),
            'averageSalary' => Employee::avg('base_salary'),
        ];
        
        // Attendance summary for today
        $attendanceSummary = [
            'present' => Attendance::whereDate('date', $today)
                                ->where('status', 'Present')
                                ->count(),
            'absent' => Attendance::whereDate('date', $today)
                                ->where('status', 'Absent')
                                ->count(),
            'late' => Attendance::whereDate('date', $today)
                                ->where('status', 'Late')
                                ->count(),
            'onLeave' => Attendance::whereDate('date', $today)
                                ->where('status', 'On Leave')
                                ->count(),
        ];
        
        // Recent payroll records
        $recentPayrolls = Payroll::with('employee')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        
        // Department distribution for payroll
        $departmentPayroll = Employee::select('department', DB::raw('SUM(base_salary) as total_salary'))
                                ->groupBy('department')
                                ->get();
        
        return view('admin.dashboard', compact(
            'stats', 
            'attendanceSummary', 
            'recentPayrolls', 
            'departmentPayroll',
            'currentMonth',
            'currentYear'
        ));
    }
}