@extends('layouts.employee')

@section('title', 'Dashboard Absensi')

@section('content')
    <div class="space-y-6">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Selamat Datang, {{ $employee->name ?? Auth::user()->name }}!
                    {{-- Jika menggunakan nama lengkap dari tabel employee/karyawan:
                    Selamat Datang, {{ $employee->nama_lengkap ?? Auth::user()->name }}!
                    --}}
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Silakan lakukan presensi masuk dan pulang Anda melalui tombol di bawah ini.</p>
                    <p>Tanggal Hari Ini: <span class="font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span></p>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tombol Presensi Masuk -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <h4 class="text-md font-medium text-gray-700 mb-2">Presensi Masuk</h4>
                        @if ($todayAttendance && $todayAttendance->check_in)
                            <div class="text-sm text-green-600 bg-green-50 p-3 rounded-md">
                                Anda sudah presensi masuk hari ini pukul: <strong class="font-semibold">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i:s') }}</strong>.
                                @if($todayAttendance->notes && Str::contains(strtolower($todayAttendance->notes), 'check-in:'))
                                    <p class="mt-1 text-xs">Catatan: {{ Str::after(Str::match('/Check-in: (.*?)(?: \| Check-out:|$)/i', $todayAttendance->notes), 'Check-in: ') }}</p>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('karyawan.absensi.clockin') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="notes_check_in" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <textarea name="notes_check_in" id="notes_check_in" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Clock In (Masuk) Sekarang - {{ \Carbon\Carbon::now()->format('H:i') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Tombol Presensi Pulang -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                         <h4 class="text-md font-medium text-gray-700 mb-2">Presensi Pulang</h4>
                        @if ($todayAttendance && $todayAttendance->check_out)
                             <div class="text-sm text-green-600 bg-green-50 p-3 rounded-md">
                                Anda sudah presensi pulang hari ini pukul: <strong class="font-semibold">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i:s') }}</strong>.
                                @if($todayAttendance->working_hours)
                                    <p class="mt-1 text-xs">Total Jam Kerja: {{ number_format($todayAttendance->working_hours, 1) }} jam</p>
                                @endif
                                @if($todayAttendance->notes && Str::contains(strtolower($todayAttendance->notes), 'check-out:'))
                                     <p class="mt-1 text-xs">Catatan: {{ Str::after(Str::match('/Check-out: (.*?)$/i', $todayAttendance->notes), 'Check-out: ') }}</p>
                                @endif
                            </div>
                        @elseif ($todayAttendance && $todayAttendance->check_in)
                            <form action="{{ route('employees.absensi.clockout') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="notes_check_out" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <textarea name="notes_check_out" id="notes_check_out" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Clock Out (Pulang) Sekarang - {{ \Carbon\Carbon::now()->format('H:i') }}
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500">Silakan lakukan presensi masuk terlebih dahulu.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Riwayat Absensi Anda (7 Hari Terakhir)
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($attendanceHistory as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y, l') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($attendance->status == 'Present')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                        @elseif($attendance->status == 'Late')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>
                                        @elseif($attendance->status == 'Absent')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absen</span>
                                        @elseif($attendance->status == 'On Leave')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Cuti</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->working_hours ? number_format($attendance->working_hours, 1).' jam' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $attendance->notes }}">
                                        {{ Str::limit($attendance->notes, 50) ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Belum ada riwayat absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $attendanceHistory->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection