@extends('admin.layout')

@section('title', 'City Management')
@section('page_title', 'City Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left 2 Columns: Cities List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-base text-slate-800">Cities Database</h4>
                    <p class="text-xs text-slate-500">List of cities, registration links, and QR codes</p>
                </div>
                <span class="text-xs bg-slate-100 px-3 py-1 rounded-full text-slate-600 font-medium">
                    {{ $cities->count() }} Cities
                </span>
            </div>

            @if($cities->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-4">City Name</th>
                                <th class="px-6 py-4">Registration Link (Encoded)</th>
                                <th class="px-6 py-4 text-center">QR Code</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @foreach($cities as $city)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- City Name -->
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800">{{ $city->name }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $city->registrations_count }} registrations</div>
                                    </td>
                                    
                                    <!-- Registration URL -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 max-w-[200px] sm:max-w-xs md:max-w-md">
                                            <code class="text-xs text-indigo-600 bg-indigo-50/50 border border-indigo-100 px-2 py-1 rounded truncate select-all">
                                                {{ route('registration.show', $city->slug) }}
                                            </code>
                                            <button onclick="copyToClipboard('{{ route('registration.show', $city->slug) }}', this)" 
                                                    class="p-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white transition-all text-slate-500 shrink-0" 
                                                    title="Copy link">
                                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <a href="{{ route('registration.show', $city->slug) }}" target="_blank" 
                                               class="p-1 rounded bg-slate-100 hover:bg-slate-200 transition-all text-slate-500 shrink-0" 
                                               title="Open registration page">
                                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            </a>
                                        </div>
                                    </td>

                                    <!-- QR Code Thumbnail -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-block relative group">
                                            @if($city->qr_code_path)
                                                <img src="{{ asset($city->qr_code_path) }}" alt="QR Code Thumbnail" 
                                                     class="w-10 h-10 object-contain border border-slate-200 rounded p-0.5 cursor-zoom-in bg-white hover:scale-110 transition-transform">
                                                <!-- Full size QR preview tooltip on hover -->
                                                <div class="absolute bottom-12 left-1/2 -translate-x-1/2 hidden group-hover:block bg-white p-3 border border-slate-200 rounded-xl shadow-xl z-50 w-48">
                                                    <img src="{{ asset($city->qr_code_path) }}" alt="Full QR Code" class="w-full h-auto">
                                                    <p class="text-[10px] text-slate-400 mt-1.5 text-center font-medium">Scan to Register</p>
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Actions (Download QR) -->
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.cities.download-qr', $city->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                            <i data-lucide="download" class="w-3 h-3"></i> Download QR
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">No cities added yet</p>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">Use the form on the right to dynamically create registration URLs & QR codes.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right 1 Column: Create City Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-8">
            <h4 class="font-bold text-base text-slate-800 mb-1">Create City Link</h4>
            <p class="text-xs text-slate-500 mb-6">Generates a dynamic URL and a QR Code instantly.</p>

            <form action="{{ route('admin.cities.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">City Name</label>
                    <input type="text" name="name" id="name" required placeholder="e.g. New York" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1.5 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium text-sm transition-all shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Generate URL & QR
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = button.querySelector('i');
            const originalClass = icon.getAttribute('data-lucide');
            
            // Swap icon to check mark
            icon.setAttribute('data-lucide', 'check');
            button.classList.remove('bg-slate-100', 'text-slate-500');
            button.classList.add('bg-emerald-600', 'text-white');
            lucide.createIcons();

            setTimeout(() => {
                icon.setAttribute('data-lucide', 'copy');
                button.classList.remove('bg-emerald-600', 'text-white');
                button.classList.add('bg-slate-100', 'text-slate-500');
                lucide.createIcons();
            }, 1500);
        });
    }
</script>
@endsection
