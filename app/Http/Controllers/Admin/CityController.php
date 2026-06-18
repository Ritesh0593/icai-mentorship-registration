<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CityController extends Controller
{
    /**
     * Display a listing of the cities.
     */
    public function index()
    {
        $cities = City::withCount('registrations')
            ->orderBy('name')
            ->get();

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
        ]);

        City::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City created successfully and QR code generated!');
    }

    /**
     * Download the QR Code image for the specified city.
     */
    public function downloadQrCode(City $city)
    {
        $fileName = 'qrcode_' . $city->slug . '.svg';
        $filePath = 'qrcodes/' . $fileName;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $city->name . '_qr_code.svg');
        }

        abort(404, 'QR Code file not found.');
    }
}
