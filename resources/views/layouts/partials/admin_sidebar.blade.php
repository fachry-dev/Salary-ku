<aside class="w-64 bg-white shadow-md flex-shrink-0">
    <div class="p-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span class="text-2xl font-semibold text-gray-800">Humaine</span>
        </a>
    </div>

    <div class="px-4 mb-4">
        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center">
            <span class="font-medium text-gray-700">Lead Inc.</span>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
        </div>
    </div>

    <nav class="px-4 space-y-2">
        <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main Menu</p>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 {{ request()->routeIs('admin.dashboard') ? 'active-nav-link' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.employees.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 {{ request()->routeIs('admin.karyawan.*') ? 'active-nav-link' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>Employee Directory</span>
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 {{ request()->routeIs('admin.absensi.*') ? 'active-nav-link' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>Attendance & Leave</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span>Recruitment</span>
        </a>
        <a href="{{ route('admin.salary.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 {{ request()->routeIs('admin.gaji.*') ? 'active-nav-link' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span>Payroll</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>Performance</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Reports & Analytics</span>
        </a>

        <p class="px-4 pt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Other</p>
         <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Settings</span>
        </a>
        <a href="#" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.755 4 3.92C16 13.09 14.828 15 12 15c-1.464 0-2.906-.835-3.772-2.333A4.026 4.026 0 006 11.17C6 9.497 7.03 8.006 8.228 9zM12 12c.667 0 1.167-.467 1.167-1.167C13.167 10.167 12.667 9.5 12 9.5s-1.167.667-1.167 1.333C10.833 11.533 11.333 12 12 12z"></path></svg>
            <span>Help Center</span>
        </a>
    </nav>

    <div class="px-6 mt-auto mb-6">
        <div class="p-4 bg-indigo-50 rounded-lg text-center">
            <svg class="w-8 h-8 mx-auto text-indigo-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <h4 class="font-semibold text-gray-800 mb-1">Upgrade to Premium Plan</h4>
            <p class="text-sm text-gray-600 mb-3">Unlock advanced AI analytics, priority support, and exclusive HR tools.</p>
            <a href="#" class="block w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-indigo-700">Upgrade Now</a>
        </div>
    </div>
</aside>