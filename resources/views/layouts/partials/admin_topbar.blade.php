<header class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Left side: Tabs -->
        <div class="hidden md:flex space-x-1">
            <a href="{{ route('admin.gaji.index') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.gaji.index') && !request()->filled('view') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                Payroll Overview
            </a>
             {{-- Placeholder tabs --}}
            <a href="#" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Employee List</a>
            <a href="#" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Organization Chart</a>
            <a href="#" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Employee Profiles</a>
        </div>

        <!-- Right side: Actions & User -->
        <div class="flex items-center space-x-4">
             {{-- Placeholder Search --}}
            <button class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
            <button class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </button>
            <div class="relative">
                <button type="button" class="flex items-center space-x-2 focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                    <span class="hidden md:inline text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    <svg class="hidden md:inline w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <!-- Dropdown menu -->
                <div id="user-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
     @stack('topbar_actions') {{-- For date picker, export, etc. --}}
</header>

@push('scripts')
<script>
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    if (userMenuButton) {
        userMenuButton.addEventListener('click', () => {
            const expanded = userMenuButton.getAttribute('aria-expanded') === 'true' || false;
            userMenuButton.setAttribute('aria-expanded', !expanded);
            userMenu.classList.toggle('hidden');
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
                userMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>
@endpush