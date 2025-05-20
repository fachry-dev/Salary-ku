<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controller Autentikasi
use App\Http\Controllers\ProfileController;

// Controller for Authentication
use App\Http\Controllers\Auth\AuthController;

// Controller Admin
use App\Http\Controllers\AttendanceController; 
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SalaryController as AdminSalaryController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\AttendanceManagementController as AdminAttendanceManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Awal & Pengalihan berdasarkan status login
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'employee') { // Changed from isKaryawan() to check role directly
            return redirect()->route('employee.dashboard');
        }
        // Fallback jika role tidak sesuai
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak dikenali.');
    }
    return redirect()->route('login');
});

// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
    // Tambahkan rute register jika perlu:
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    // Route::post('register', [RegisteredUserController::class, 'store']);
});

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

    // Rute Dashboard Umum (akan di-redirect berdasarkan role)
    Route::get('/dashboard', function () {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'employee') { // Changed from isKaryawan()
            return redirect()->route('employee.dashboard');
        }
        return redirect()->route('login'); // Fallback
    })->name('dashboard');


    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Untuk update info
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update'); // Untuk update password
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Jika ada hapus akun


    // Rute untuk Karyawan
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
        Route::get('/attendance/history', [AttendanceController::class, 'dashboard'])->name('attendances.history');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Employee Routes
        Route::resource('employees', AdminEmployeeController::class);

        // Attendance Management
        Route::resource('attendances', AdminAttendanceManagementController::class);

        // Salary Management
        Route::get('salary', [AdminSalaryController::class, 'index'])->name('salary.index');
        Route::get('salary/create', [AdminSalaryController::class, 'createForm'])->name('salary.create');
        Route::post('salary/calculate-store', [AdminSalaryController::class, 'calculateAndStore'])->name('salary.calculate.store');
        Route::get('salary/{salary}', [AdminSalaryController::class, 'show'])->name('salary.show');
        Route::get('salary/{salary}/edit', [AdminSalaryController::class, 'edit'])->name('salary.edit');
        Route::put('salary/{salary}', [AdminSalaryController::class, 'update'])->name('salary.update');
        Route::delete('salary/{salary}', [AdminSalaryController::class, 'destroy'])->name('salary.destroy');
        Route::get('salary/{salary}/print-slip', [AdminSalaryController::class, 'printSlip'])->name('salary.slip');
    });
});