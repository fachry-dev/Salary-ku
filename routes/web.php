<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controller untuk Employee
use App\Http\Controllers\AttendanceController;

// Controller untuk Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\SalaryController;

// Controller untuk Profile
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
        Auth::logout();
        return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
    }
    return redirect()->route('login');
});

// Use Laravel's built-in auth routes instead of custom ones
// require _DIR_.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('employee.dashboard');
            }
        }
        return redirect()->route('login');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Rute untuk Employee (previously Karyawan)
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Employee management
        Route::resource('employees', EmployeeController::class);

        // Attendance Management
        Route::resource('attendances', AttendanceManagementController::class);

        // Salary Management
        Route::get('salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::get('salary/create', [SalaryController::class, 'createForm'])->name('salary.create');
        Route::post('salary/calculate-store', [SalaryController::class, 'calculateAndStore'])->name('salary.calculate.store');
        Route::get('salary/{salary}', [SalaryController::class, 'show'])->name('salary.show');
        Route::get('salary/{salary}/edit', [SalaryController::class, 'edit'])->name('salary.edit');
        Route::put('salary/{salary}', [SalaryController::class, 'update'])->name('salary.update');
        Route::delete('salary/{salary}', [SalaryController::class, 'destroy'])->name('salary.destroy');
        Route::get('salary/{salary}/print-slip', [SalaryController::class, 'printSlip'])->name('salary.print.slip');
    });
});