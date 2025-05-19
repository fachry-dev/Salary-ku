<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN (untuk contoh) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        @layer utilities {
          .content-auto {
            content-visibility: auto;
          }
        }
        /* Tambahan custom style jika perlu */
        .active-nav-link {
            background-color: #e0e7ff; /* bg-indigo-100 */
            color: #4f46e5; /* text-indigo-700 */
            font-weight: 600;
        }
    </style>

    <!-- Chart.js via CDN (untuk chart) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('layouts.partials.admin_sidebar')

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            @include('layouts.partials.admin_topbar')

            <!-- Page Content -->
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>