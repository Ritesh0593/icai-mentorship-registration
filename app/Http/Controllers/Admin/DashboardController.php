<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Registration;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home with metrics and chart data.
     */
    public function index()
    {
        $totalCities = City::count();
        $totalRegistrations = Registration::count();
        $todayRegistrations = Registration::whereDate('created_at', now()->today())->count();

        // Fetch cities with registration counts for Chart.js
        $citiesData = City::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->get();

        $chartLabels = $citiesData->pluck('name')->toArray();
        $chartValues = $citiesData->pluck('registrations_count')->toArray();

        return view('admin.dashboard', compact(
            'totalCities',
            'totalRegistrations',
            'todayRegistrations',
            'chartLabels',
            'chartValues'
        ));
    }

    /**
     * Display a listing of registrations with city filter.
     */
    public function registrations(Request $request)
    {
        $cities = City::orderBy('name')->get();
        $selectedCity = $request->query('city_id');
        $search = $request->query('search');

        $query = Registration::with('city')->latest();

        if ($selectedCity) {
            $query->where('city_id', $selectedCity);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $registrations = $query->paginate(20)->withQueryString();

        return view('admin.registrations.index', compact('registrations', 'cities', 'selectedCity', 'search'));
    }

    /**
     * Export registrations data to CSV.
     */
    public function export(Request $request)
    {
        $selectedCity = $request->query('city_id');
        $search = $request->query('search');
        $query = Registration::with('city')->latest();

        if ($selectedCity) {
            $query->where('city_id', $selectedCity);
            $city = City::find($selectedCity);
            $cityName = $city ? $city->name : 'filtered';
            $fileName = 'registrations_' . strtolower(str_replace(' ', '_', $cityName)) . '_' . date('Y-m-d') . '.csv';
        } else {
            $fileName = 'registrations_all_' . date('Y-m-d') . '.csv';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
            $fileName = str_replace('.csv', '_search_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $search)) . '.csv', $fileName);
        }

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for proper excel formatting
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($handle, ['ID', 'City', 'Name', 'Email', 'Phone', 'Participant Category', 'Area of Mentorship', 'Registration Date']);

            // Chunk database queries for memory optimization
            $query->chunk(500, function ($registrations) use ($handle) {
                foreach ($registrations as $reg) {
                    fputcsv($handle, [
                        $reg->id,
                        $reg->city->name ?? 'Deleted City',
                        $reg->name,
                        $reg->email,
                        $reg->phone,
                        $reg->participant_category,
                        $reg->mentorship_area,
                        $reg->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);

        return $response;
    }
}
