<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Event Dashboard') - Admin Panel</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="h-full text-slate-800 flex flex-col md:flex-row">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 text-slate-100 flex flex-col border-r border-slate-800">
        <!-- Brand -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800 bg-slate-950">
            <span class="flex items-center gap-2.5 font-bold text-slate-200 tracking-wide">
                <img src="{{ asset('logo_0.png') }}" alt="ICAI" class="h-8 w-auto">
                <div class="leading-none">
                    <span class="text-xs font-extrabold text-white block">MSME Mentorship</span>
                    <span class="text-[9px] font-bold text-amber-500 tracking-wider uppercase mt-0.5 block">ICAI Drive 2026</span>
                </div>
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.cities.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.cities.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                Cities
            </a>
            <a href="{{ route('admin.registrations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.registrations.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                Registrations
            </a>
        </nav>

        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-semibold text-sm border border-indigo-500/20">
                        A
                    </div>
                    <div class="text-xs">
                        <p class="font-medium text-slate-200">Administrator</p>
                        <p class="text-slate-500">Event Manager</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-800 hover:text-red-400 transition-all" title="Logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Top bar (Header) -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between">
            <h1 class="font-bold text-lg text-slate-800">@yield('page_title', 'Dashboard')</h1>
            <div class="text-xs text-slate-400 font-medium">
                {{ now()->format('l, d M Y') }}
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 md:p-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
