<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controller Autentikasi
use App\Http\Controllers\ProfileController;

// Controller Profile
use App\Http\Controllers\Auth\AuthController;

// Controller Karyawan (untuk absensi)
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Controller Admin
use App\Http\Controllers\AttendanceController; // Sebelumnya AbsensiController
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SalaryController as AdminSalaryController; // Sebelumnya GajiController
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController; // Sebelumnya KaryawanController
use App\Http\Controllers\Admin\AttendanceManagementController as AdminAttendanceManagementController; // Sebelumnya KelolaAbsensiController

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
        } elseif (Auth::user()->isKaryawan()) { // Asumsi ada method isKaryawan() di model User
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
        } elseif (Auth::user()->isKaryawan()) {
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
    Route::middleware('role:karyawan')->prefix('karyawan')->name('employee.')->group(function () {
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('absensi.clockin');
        Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('absensi.clockout');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Menggunakan nama resource 'karyawan' untuk URL, tapi controller EmployeeController
        Route::resource('karyawan', AdminEmployeeController::class); // CRUD Karyawan

        // Kelola Absensi (Attendance Management)
        Route::get('absensi', [AdminAttendanceManagementController::class, 'index'])->name('absensi.index');
        Route::get('absensi/create', [AdminAttendanceManagementController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AdminAttendanceManagementController::class, 'store'])->name('absensi.store');
        Route::get('absensi/{absensi}/edit', [AdminAttendanceManagementController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi/{absensi}', [AdminAttendanceManagementController::class, 'update'])->name('absensi.update');
        Route::delete('absensi/{absensi}', [AdminAttendanceManagementController::class, 'destroy'])->name('absensi.destroy');

        // Kelola Gaji (Salary)
        Route::get('gaji', [AdminSalaryController::class, 'index'])->name('gaji.index');
        Route::get('gaji/create', [AdminSalaryController::class, 'createForm'])->name('gaji.create');
        Route::post('gaji/calculate-store', [AdminSalaryController::class, 'calculateAndStore'])->name('gaji.calculate.store');
        Route::get('gaji/{gaji}', [AdminSalaryController::class, 'show'])->name('gaji.show');
        Route::get('gaji/{gaji}/edit', [AdminSalaryController::class, 'edit'])->name('gaji.edit');
        Route::put('gaji/{gaji}', [AdminSalaryController::class, 'update'])->name('gaji.update');
        Route::delete('gaji/{gaji}', [AdminSalaryController::class, 'destroy'])->name('gaji.destroy');
        Route::get('gaji/{gaji}/print-slip', [AdminSalaryController::class, 'printSlip'])->name('gaji.print.slip');
    });
});