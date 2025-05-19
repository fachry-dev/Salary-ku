@extends('layouts.admin')

@section('title', 'Payroll Overview')

@push('topbar_actions')
<div class="container mx-auto px-6 py-3 border-t border-gray-200 bg-gray-50">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800 mb-2 md:mb-0">Payroll Overview</h1>
        <div class="flex items-center space-x-3">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <select name="bulan" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($listBulan as $num => $name)
                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                     @foreach($listTahun as $y)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                    Filter
                </button>
            </form>
            <span class="text-gray-400">|</span>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50 flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export Payroll</span>
            </button>
            <a href="{{ route('admin.salary.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add a New Payroll</span>
            </a>
        </div>
    </div>
</div>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Info Message -->
        <div class="p-4 bg-blue-50 text-blue-700 border border-blue-200 rounded-md flex items-start space-x-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm">
                Heads up! Payroll is due in 3 days. Please ensure all employee data, attendance logs, and bonuses are finalized before April 17, 2025 to avoid delays in payment processing.
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Payroll This Month</h3>
                    {{-- <span class="text-xs text-green-500">+8.2% from last month</span> --}}
                </div>
                <p class="text-3xl font-semibold text-gray-800">${{ number_format($totalPayrollThisMonth ?? 0, 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Employees Paid</h3>
                    {{-- <span class="text-xs text-green-500">+3 from last month</span> --}}
                </div>
                <p class="text-3xl font-semibold text-gray-800">{{ $totalEmployeesPaid ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-500">Average Salary</h3>
                    {{-- <span class="text-xs text-green-500">+$94 from last month</span> --}}
                </div>
                <p class="text-3xl font-semibold text-gray-800">${{ number_format($averageSalary ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Monthly Payroll Trend</h3>
                    <select class="text-sm border-gray-300 rounded-md shadow-sm">
                        <option>Last 6 months</option>
                        <option>Last 12 months</option>
                    </select>
                </div>
                <div class="h-72">
                    <canvas id="monthlyPayrollChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Payroll Distribution by Department</h3>
                <p class="text-3xl font-semibold text-gray-800 mb-1">${{ number_format($payrollDistributionData['total'] ?? 0, 2) }}</p>
                <p class="text-xs text-green-500 mb-4">+{{ $payrollDistributionData['change_from_last_year'] ?? 0 }}% from last year</p>
                <div class="h-60">
                    <canvas id="payrollDistributionChart"></canvas>
                </div>
                 <div class="flex justify-center space-x-4 mt-2 text-xs text-gray-500">
                    <span><span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-1"></span>Salary</span>
                    {{-- <span><span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>Bonus</span> --}}
                </div>
            </div>
        </div>

        <!-- Payroll List -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 md:mb-0">Payroll List (Recent)</h3>
                <div class="flex items-center space-x-2">
                    <input type="text" placeholder="Search employee..." class="text-sm border-gray-300 rounded-md shadow-sm w-full md:w-auto">
                    {{-- Placeholder filters --}}
                    <select class="text-sm border-gray-300 rounded-md shadow-sm">
                        <option>Select department</option>
                    </select>
                    <select class="text-sm border-gray-300 rounded-md shadow-sm">
                        <option>Status</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Pokok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potongan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Bersih</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($payrollList as $gaji)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $gaji->karyawan->nama_lengkap ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $gaji->karyawan->nip ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::create()->month($gaji->bulan)->monthName }} {{ $gaji->tahun }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($gaji->tanggal_pembayaran)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Paid ({{ $gaji->tanggal_pembayaran->format('M d, Y') }})
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($gaji->gaji_pokok, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">${{ number_format($gaji->potongan, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">${{ number_format($gaji->gaji_bersih, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.salary.show', $gaji->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                                    <a href="{{ route('admin.salary.slip', $gaji->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No payroll data for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">
                <a href="{{ route('admin.salary.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All Payroll Data →</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Monthly Payroll Trend Chart
    const monthlyPayrollCtx = document.getElementById('monthlyPayrollChart');
    if (monthlyPayrollCtx) {
        const monthlyPayrollData = @json($monthlyPayrollTrendData);
        const peakData = monthlyPayrollData.example_peak; // Ambil data peak dari controller

        new Chart(monthlyPayrollCtx, {
            type: 'line',
            data: {
                labels: monthlyPayrollData.labels,
                datasets: [{
                    label: 'Monthly Payroll',
                    data: monthlyPayrollData.data,
                    borderColor: 'rgb(79, 70, 229)', // indigo-600
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(79, 70, 229)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y.toLocaleString();
                                }
                                // Tambahkan info peak jika label cocok
                                if (context.label === peakData.label) {
                                    label += ` (Peak: $${peakData.value.toLocaleString()}, ${peakData.change}% from previous)`;
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                // Untuk anotasi peak (lebih advanced, bisa pakai plugin chartjs-plugin-annotation)
                // Ini hanya contoh visual tooltip, bukan anotasi permanen di chart
            }
        });
    }

    // Payroll Distribution by Department Chart
    const payrollDistributionCtx = document.getElementById('payrollDistributionChart');
    if (payrollDistributionCtx) {
        const payrollDistributionData = @json($payrollDistributionData);
        new Chart(payrollDistributionCtx, {
            type: 'bar',
            data: {
                labels: payrollDistributionData.labels,
                datasets: [{
                    label: 'Payroll by Dept',
                    data: payrollDistributionData.data,
                    backgroundColor: [ // Array warna untuk setiap bar
                        'rgba(59, 130, 246, 0.7)', // blue-500
                        'rgba(34, 197, 94, 0.7)',  // green-500
                        'rgba(234, 179, 8, 0.7)',  // yellow-500
                        'rgba(249, 115, 22, 0.7)', // orange-500
                        'rgba(139, 92, 246, 0.7)', // violet-500
                        'rgba(107, 114, 128, 0.7)' // gray-500
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(249, 115, 22)',
                        'rgb(139, 92, 246)',
                        'rgb(107, 114, 128)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                         ticks: {
                            callback: function(value, index, values) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Sembunyikan legend bawaan karena kita buat custom di HTML
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush