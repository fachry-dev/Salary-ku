<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controller untuk Autentikasi (asumsi menggunakan default atau sudah ada)
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Controller untuk Karyawan (yang baru)
use App\Http\Controllers\AttendanceController; // Menggantikan AbsensiController

// Controller untuk Admin (yang baru)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController; // Menggantikan KaryawanController
use App\Http\Controllers\Admin\AttendanceManagementController as AdminAttendanceManagementController; // Menggantikan KelolaAbsensiController
use App\Http\Controllers\Admin\SalaryController as AdminSalaryController; // Menggantikan GajiController

// Controller untuk Profile (asumsi menggunakan default atau sudah ada)
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->isKaryawan()) {
            return redirect()->route('karyawan.dashboard');
        }
        Auth::logout();
        return redirect()->route('login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
    }
    return redirect()->route('login');
});

// Rute Autentikasi (Sesuaikan jika Anda tidak pakai Breeze/UI default)
Route::get('login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
// require __DIR__.'/auth.php'; // Aktifkan ini jika Anda pakai Breeze/UI dan komentari 3 baris di atas

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->isKaryawan()) {
                return redirect()->route('karyawan.dashboard');
            }
        }
        return redirect()->route('login');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Rute untuk Karyawan
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        // Menggunakan AttendanceController yang baru
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('absensi.clockin');
        Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('absensi.clockout');
    });

    // Rute untuk Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Karyawan (Employee)
        // Resource 'karyawan' untuk URL, tapi controller-nya AdminEmployeeController
        Route::resource('karyawan', AdminEmployeeController::class);

        // Kelola Absensi (Attendance Management)
        // URL tetap 'absensi' untuk konsistensi, tapi controller-nya AdminAttendanceManagementController
        Route::get('absensi', [AdminAttendanceManagementController::class, 'index'])->name('absensi.index');
        Route::get('absensi/create', [AdminAttendanceManagementController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AdminAttendanceManagementController::class, 'store'])->name('absensi.store');
        Route::get('absensi/{absensi}/edit', [AdminAttendanceManagementController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi/{absensi}', [AdminAttendanceManagementController::class, 'update'])->name('absensi.update');
        Route::delete('absensi/{absensi}', [AdminAttendanceManagementController::class, 'destroy'])->name('absensi.destroy');

        // Kelola Gaji (Salary)
        // URL tetap 'gaji' untuk konsistensi, tapi controller-nya AdminSalaryController
        Route::get('gaji', [AdminSalaryController::class, 'index'])->name('salary.index');
        Route::get('gaji/create', [AdminSalaryController::class, 'createForm'])->name('salary.create');
        Route::post('gaji/calculate-store', [AdminSalaryController::class, 'calculateAndStore'])->name('gaji.calculate.store');
        Route::get('gaji/{gaji}', [AdminSalaryController::class, 'show'])->name('gaji.show');
        Route::get('gaji/{gaji}/edit', [AdminSalaryController::class, 'edit'])->name('gaji.edit');
        Route::put('gaji/{gaji}', [AdminSalaryController::class, 'update'])->name('gaji.update');
        Route::delete('gaji/{gaji}', [AdminSalaryController::class, 'destroy'])->name('gaji.destroy');
        Route::get('gaji/{gaji}/print-slip', [AdminSalaryController::class, 'printSlip'])->name('gaji.print.slip');
    });
});