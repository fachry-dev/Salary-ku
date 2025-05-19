@extends('layouts.admin')

@section('title', 'Payroll Management')

@push('topbar_actions')
<div class="container mx-auto px-6 py-3 border-t border-gray-200 bg-gray-50">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800 mb-2 md:mb-0">Payroll List</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                ← Back to Overview
            </a>
            <a href="{{ route('admin.salary.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Calculate New Payroll</span>
            </a>
        </div>
    </div>
</div>
@endpush


@section('content')
    <div class="bg-white p-6 rounded-lg shadow">
        <form method="GET" action="{{ route('admin.salary.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="karyawan_id_filter" class="block text-sm font-medium text-gray-700">Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawans as $id => $nama)
                            <option value="{{ $id }}" {{ request('karyawan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bulan_filter" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <select name="bulan" id="bulan_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($listBulan as $num => $name)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tahun_filter" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select name="tahun" id="tahun_filter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($listTahun as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Filter Data
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hadir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Pokok</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Potongan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Bersih</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($gajis as $gaji)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $gaji->karyawan->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $gaji->karyawan->nip }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::create()->month($gaji->bulan)->monthName }} {{ $gaji->tahun }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $gaji->total_hadir }} hari</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Rp {{ number_format($gaji->potongan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($gaji->tanggal_pembayaran)
                                    {{ $gaji->tanggal_pembayaran->format('d M Y') }}
                                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.salary.show', $gaji->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2" title="View Detail">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('admin.salary.edit', $gaji->id) }}" class="text-yellow-500 hover:text-yellow-700 mr-2" title="Edit">
                                     <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="{{ route('admin.salary.slip', $gaji->id) }}" target="_blank" class="text-blue-500 hover:text-blue-700 mr-2" title="Print Slip">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <form action="{{ route('admin.salary.destroy', $gaji->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data gaji ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data gaji untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $gajis->appends(request()->query())->links() }}
        </div>
    </div>
@endsection