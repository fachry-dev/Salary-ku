<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance; // Pastikan model ini ada dan field-nya sesuai
use App\Models\Employee;   // Pastikan model ini ada dan field-nya sesuai
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Hanya jika ada method yang memerlukan Auth di controller ini

class AttendanceManagementController extends Controller
{
    /**
     * Display a listing of the attendance records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mengambil semua karyawan untuk filter dropdown, diurutkan berdasarkan nama
        $employees = Employee::orderBy('name')->pluck('name', 'id'); // Lebih efisien untuk dropdown

        // Mengambil input filter dari request, dengan nilai default jika tidak ada
        $selectedEmployeeId = $request->input('employee_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Query dasar untuk mengambil data absensi
        $query = Attendance::with('employee') // Eager load relasi employee
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc') // Urutkan berdasarkan tanggal terbaru
            ->orderBy('employee_id'); // Kemudian berdasarkan ID karyawan

        // Terapkan filter karyawan jika dipilih
        if ($selectedEmployeeId) {
            $query->where('employee_id', $selectedEmployeeId);
        }

        // Paginasi hasil query
        $attendances = $query->paginate(15)->withQueryString(); // withQueryString agar filter tetap saat paginasi

        // Data untuk filter bulan dan tahun di view
        $listBulan = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create(null, $m)->monthName])->all();
        $listTahun = collect(range(Carbon::now()->year - 5, Carbon::now()->year + 1))->mapWithKeys(fn($y) => [$y => $y])->all();


        return view('admin.attendance.index', compact(
            'attendances',
            'employees',
            'selectedEmployeeId',
            'month',
            'year',
            'listBulan',
            'listTahun'
        ));
    }

    /**
     * Show the form for creating a new attendance record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->pluck('name', 'id');
        // Definisikan status yang valid untuk dropdown di form
        $statuses = ['Present' => 'Present', 'Absent' => 'Absent', 'Late' => 'Late', 'On Leave' => 'On Leave'];
        return view('admin.attendance.create', compact('employees', 'statuses'));
    }

    /**
     * Store a newly created attendance record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s,H:i', // Mendukung format H:i atau H:i:s
            'check_out' => 'nullable|date_format:H:i:s,H:i|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late,On Leave', // Sesuaikan dengan enum di DB jika perlu
            'working_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        // Cek apakah sudah ada absensi untuk karyawan dan tanggal tersebut
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $request->date) // Gunakan whereDate untuk perbandingan tanggal
            ->first();
        if ($existingAttendance) {
            return back()->withInput()->with('error', 'Attendance record for this employee on this date already exists.');
        }

        $data = $request->only(['employee_id', 'date', 'check_in', 'check_out', 'status', 'notes']);

        // Kalkulasi jam kerja jika check_in dan check_out ada
        if ($request->filled('check_in') && $request->filled('check_out')) {
            try {
                $checkInTime = Carbon::createFromFormat('H:i:s', Carbon::parse($request->check_in)->format('H:i:s'));
                $checkOutTime = Carbon::createFromFormat('H:i:s', Carbon::parse($request->check_out)->format('H:i:s'));
                // Handle jika checkout adalah hari berikutnya (kasus kerja malam, jarang untuk absensi harian standar)
                // Jika checkout < checkin, anggap hari berikutnya (atau perlu logika lebih kompleks)
                // Untuk kesederhanaan, kita anggap selalu di hari yang sama atau user input manual working_hours
                if ($checkOutTime->lt($checkInTime)) {
                    // Opsi: beri error atau biarkan input manual working_hours
                    // return back()->withInput()->with('error', 'Check-out time cannot be earlier than check-in time on the same day.');
                }
                $data['working_hours'] = round($checkOutTime->diffInSeconds($checkInTime) / 3600, 2); // Jam kerja dalam desimal (misal 7.5 jam)
            } catch (\Exception $e) {
                // Gagal parse waktu, biarkan working_hours dari input atau null
                $data['working_hours'] = $request->input('working_hours');
            }
        } else {
            $data['working_hours'] = $request->input('working_hours');
        }

        // Jika status bukan 'Present' atau 'Late', kosongkan check_in, check_out, dan working_hours
        if (!in_array($request->status, ['Present', 'Late'])) {
            $data['check_in'] = null;
            $data['check_out'] = null;
            $data['working_hours'] = null;
        }


        Attendance::create($data);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record added successfully.');
    }

    /**
     * Show the form for editing the specified attendance record.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\View\View
     */
    public function edit(Attendance $attendance) // Route Model Binding
    {
        $employees = Employee::orderBy('name')->pluck('name', 'id');
        $statuses = ['Present' => 'Present', 'Absent' => 'Absent', 'Late' => 'Late', 'On Leave' => 'On Leave'];
        return view('admin.attendance.edit', compact('attendance', 'employees', 'statuses'));
    }

    /**
     * Update the specified attendance record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Attendance $attendance) // Route Model Binding
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s,H:i',
            'check_out' => 'nullable|date_format:H:i:s,H:i|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late,On Leave',
            'working_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        // Cek duplikasi jika tanggal atau karyawan diubah
        if ($request->employee_id != $attendance->employee_id || $request->date != $attendance->date->format('Y-m-d')) {
            $existingAttendance = Attendance::where('employee_id', $request->employee_id)
                ->whereDate('date', $request->date)
                ->where('id', '!=', $attendance->id) // Abaikan record saat ini
                ->first();
            if ($existingAttendance) {
                return back()->withInput()->with('error', 'Attendance record for this employee on this date already exists.');
            }
        }

        $data = $request->only(['employee_id', 'date', 'check_in', 'check_out', 'status', 'notes']);

        if ($request->filled('check_in') && $request->filled('check_out')) {
            try {
                $checkInTime = Carbon::createFromFormat('H:i:s', Carbon::parse($request->check_in)->format('H:i:s'));
                $checkOutTime = Carbon::createFromFormat('H:i:s', Carbon::parse($request->check_out)->format('H:i:s'));
                if ($checkOutTime->lt($checkInTime)) {
                    // Opsi: error atau biarkan input manual
                }
                $data['working_hours'] = round($checkOutTime->diffInSeconds($checkInTime) / 3600, 2);
            } catch (\Exception $e) {
                $data['working_hours'] = $request->input('working_hours');
            }
        } else {
            $data['working_hours'] = $request->input('working_hours');
        }

        // Jika status bukan 'Present' atau 'Late', kosongkan check_in, check_out, dan working_hours
        if (!in_array($request->status, ['Present', 'Late'])) {
            $data['check_in'] = null;
            $data['check_out'] = null;
            $data['working_hours'] = null;
        }

        $attendance->update($data);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified attendance record from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Attendance $attendance) // Route Model Binding
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted successfully.');
    }



    public function dashboard()
    {
        $employee = Auth::user()->employee; // Pastikan relasi 'employee' ada di model User
        if (!$employee) {
            Auth::logout();
            // Tambahkan $request jika menggunakan session invalidation
            // $request->session()->invalidate();
            // $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan. Silakan hubungi admin.');
        }

        $today = Carbon::today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $attendanceHistory = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('employee.dashboard', compact('todayAttendance', 'attendanceHistory', 'employee'));
    }
}
