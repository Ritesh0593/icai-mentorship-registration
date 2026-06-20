@extends('admin.layout')

@section('title', 'Event Dashboard')
@section('page_title', 'Overview Dashboard')

@section('content')
<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Cities Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Active Cities</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalCities }}</h3>
            <p class="text-xs text-slate-500 mt-1">Cities registered for the event</p>
        </div>
        <div class="w-12 h-12 bg-indigo-500/10 rounded-xl border border-indigo-500/20 flex items-center justify-center text-indigo-600">
            <i data-lucide="map-pin" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Total Registrations Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Registrations</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalRegistrations }}</h3>
            <p class="text-xs text-slate-500 mt-1">Total attendees registered</p>
        </div>
        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl border border-emerald-500/20 flex items-center justify-center text-emerald-600">
            <i data-lucide="users" class="w-6 h-6"></i>
        </div>
    </div>

    <!-- Today's Registrations Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Today's Registrations</p>
            <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $todayRegistrations }}</h3>
            <p class="text-xs text-slate-500 mt-1">Registrations received today</p>
        </div>
        <div class="w-12 h-12 bg-amber-500/10 rounded-xl border border-amber-500/20 flex items-center justify-center text-amber-600">
            <i data-lucide="sparkles" class="w-6 h-6"></i>
        </div>
    </div>
</div>

<!-- Graph Section -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h4 class="font-bold text-base text-slate-800">City-wise Registration Metrics</h4>
            <p class="text-xs text-slate-500">Compare registrations counts across all cities</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3.5 h-3.5 bg-indigo-600 rounded-full inline-block"></span>
            <span class="text-xs font-medium text-slate-600">Registrations Count</span>
        </div>
    </div>

    @if(count($chartLabels) > 0)
        <!-- Chart Container -->
        <div class="h-96 relative">
            <canvas id="registrationsChart"></canvas>
        </div>
    @else
        <!-- Empty State -->
        <div class="h-64 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mb-3">
                <i data-lucide="bar-chart-2" class="w-8 h-8"></i>
            </div>
            <h5 class="font-semibold text-slate-700 text-sm">No data available yet</h5>
            <p class="text-xs text-slate-400 max-w-xs mt-1">Create cities and start collecting registrations to view metrics.</p>
        </div>
    @endif
</div>

<!-- Category Stats Section -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <div>
        <h4 class="font-bold text-base text-slate-800">Category-wise Summary</h4>
        <p class="text-xs text-slate-500 mb-6">Overview of cities and registrations grouped by scale category</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Scale Criteria (Range)</th>
                    <th class="px-6 py-4 text-center">Total Cities</th>
                    <th class="px-6 py-4 text-center">Total Registrations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @php
                    $ranges = [
                        'Micro' => '1 to 500',
                        'Small' => '501 to 1000',
                        'Medium' => '1001 to 2500',
                        'Large' => '2501 to 5000',
                        'Mega' => '5000>'
                    ];
                @endphp
                @foreach($categoriesStats as $cat)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800">{{ $cat->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-500">
                                {{ $ranges[$cat->name] ?? 'Custom' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-slate-800">
                            {{ $cat->cities_count }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-indigo-600">
                            {{ $cat->registrations_count }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@if(count($chartLabels) > 0)
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('registrationsChart').getContext('2d');
        
        // Gradient fill for chart bars
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.95)'); // indigo-600
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)'); 

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Registrations',
                    data: {!! json_encode($chartValues) !!},
                    backgroundColor: gradient,
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: {
                            family: 'Plus Jakarta Sans',
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Plus Jakarta Sans'
                        },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11
                            },
                            color: '#64748b',
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
