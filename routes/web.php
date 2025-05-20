<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controller untuk Auth
use App\Http\Controllers\Auth\AuthController;

// Controller untuk Karyawan
use App\Http\Controllers\AttendanceController;

// Controller untuk Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\SalaryController;

// Controller untuk Profil
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    }
    return redirect()->route('login');
});

// Rute-rute Autentikasi
Route::get('login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

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

    // Rute untuk Karyawan
    Route::middleware('role:karyawan')->prefix('karyawan')->name('employee.')->group(function () {
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('absensi.clockin');
        Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('absensi.clockout');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Karyawan
        Route::resource('karyawan', EmployeeController::class);

        // Manajemen Absensi
        Route::resource('absensi', AttendanceManagementController::class);

        // Manajemen Gaji
        Route::get('gaji', [SalaryController::class, 'index'])->name('gaji.index');
        Route::get('gaji/create', [SalaryController::class, 'createForm'])->name('gaji.create');
        Route::post('gaji/calculate-store', [SalaryController::class, 'calculateAndStore'])->name('gaji.calculate.store');
        Route::get('gaji/{gaji}', [SalaryController::class, 'show'])->name('gaji.show');
        Route::get('gaji/{gaji}/edit', [SalaryController::class, 'edit'])->name('gaji.edit');
        Route::put('gaji/{gaji}', [SalaryController::class, 'update'])->name('gaji.update');
        Route::delete('gaji/{gaji}', [SalaryController::class, 'destroy'])->name('gaji.destroy');
        Route::get('gaji/{gaji}/print-slip', [SalaryController::class, 'printSlip'])->name('gaji.print.slip');
    });
});