@extends('admin.layout')

@section('title', 'Registrations')
@section('page_title', 'Attendees Registrations')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
    
    <!-- Table Header & Filters -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h4 class="font-bold text-base text-slate-800">Registrations Records</h4>
            <p class="text-xs text-slate-500">Track and filter registration submissions across all locations</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Form (Combined Search + City Filter) -->
            <form action="{{ route('admin.registrations.index') }}" method="GET" class="flex flex-wrap items-center gap-3" id="filterForm">
                
                <!-- Search Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..."
                           class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all w-56 sm:w-64">
                </div>

                <!-- City Filter -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    </div>
                    <select name="city_id" id="city_filter" onchange="document.getElementById('filterForm').submit()"
                            class="pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all appearance-none cursor-pointer">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $selectedCity == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                    </div>
                </div>

                <!-- Search Button -->
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold transition-all">
                    Search
                </button>

                <!-- Reset Button (If active filter exists) -->
                @if(request('search') || $selectedCity)
                    <a href="{{ route('admin.registrations.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-all">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Export Button -->
            <a href="{{ route('admin.registrations.export', ['city_id' => $selectedCity, 'search' => request('search')]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-md shadow-emerald-600/10 hover:shadow-emerald-600/20 active:scale-[0.98]">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Registrations Table -->
    @if($registrations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Attendee Name</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">City</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Mentorship Interest</th>
                        <th class="px-6 py-4 text-right">Registration Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @foreach($registrations as $reg)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-mono text-xs">#{{ $reg->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $reg->name }}</td>
                            <td class="px-6 py-4">
                                <div class="text-slate-800 font-medium text-xs">{{ $reg->email }}</div>
                                <div class="text-slate-400 text-xs mt-0.5">{{ $reg->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
                                    {{ $reg->city->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">
                                    {{ $reg->participant_category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 max-w-[150px] truncate" title="{{ $reg->mentorship_area }}">
                                {{ $reg->mentorship_area }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 text-xs">
                                {{ $reg->created_at->format('M d, Y') }}
                                <span class="text-slate-400 block text-[10px] mt-0.5">{{ $reg->created_at->format('h:i A') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $registrations->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="py-16 text-center">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700">No registrations found</p>
            <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                {{ $selectedCity ? 'There are no registrations for this city yet.' : 'Registrations will appear here once attendees start signing up.' }}
            </p>
        </div>
    @endif
</div>
@endsection
