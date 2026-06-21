<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    /**
     * Display a listing of the cities.
     */
    public function index()
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $cities = City::with(['category'])->withCount('registrations')
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.cities.index', compact('cities', 'categories'));
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'category_id' => 'required|exists:categories,id',
        ]);

        City::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City created successfully and QR code generated!');
    }

    /**
     * Download the QR Code image for the specified city.
     */
    public function downloadQrCode(City $city)
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $fileName = 'qrcode_' . $city->slug . '.svg';
        $filePath = 'qrcodes/' . $fileName;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $city->name . '_qr_code.svg');
        }

        abort(404, 'QR Code file not found.');
    }

    /**
     * Export all cities to a CSV file.
     */
    public function exportCsv()
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $cities = City::with('category')->withCount('registrations')->orderBy('name')->get();
        $fileName = 'cities_export_' . date('Y-m-d') . '.csv';

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($cities) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($handle, ['ID', 'City Name', 'Category', 'Registrations Count', 'Registration URL', 'QR Code Link']);

            foreach ($cities as $city) {
                fputcsv($handle, [
                    $city->id,
                    $city->name,
                    $city->category->name ?? 'N/A',
                    $city->registrations_count,
                    route('registration.show', $city->slug),
                    asset($city->qr_code_path)
                ]);
            }

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

    /**
     * Download all QR codes bundled in a single ZIP file.
     */
    public function downloadAllQrCodes()
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $cities = City::all();

        $zip = new \ZipArchive();
        $zipFileName = 'all_qr_codes_' . date('Y-m-d') . '.zip';
        
        // Define path in storage/app/temp
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $zipFilePath = $tempDir . '/' . $zipFileName;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $addedFilesCount = 0;
            foreach ($cities as $city) {
                $fileName = 'qrcode_' . $city->slug . '.svg';
                $filePath = storage_path('app/public/qrcodes/' . $fileName);

                if (file_exists($filePath)) {
                    $zipName = str_replace(' ', '_', $city->name) . '_qr_code.svg';
                    $zip->addFile($filePath, $zipName);
                    $addedFilesCount++;
                }
            }
            $zip->close();

            if ($addedFilesCount > 0) {
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            }
            
            // Delete empty zip file
            @unlink($zipFilePath);
            return back()->with('error', 'No QR code files found in storage to bundle.');
        }

        return back()->with('error', 'Failed to generate ZIP archive.');
    }

    /**
     * Bulk upload cities from a CSV file.
     */
    public function bulkUpload(Request $request)
    {
        if (session('admin_role') !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return back()->with('error', 'Unable to open the uploaded file.');
        }

        $header = true;
        $importedCount = 0;
        $skippedCount = 0;

        // Get all categories for fast mapping
        $categoriesMap = \App\Models\Category::all()->pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtolower($name) => $id];
        })->toArray();

        // Start transaction for speed and safety
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Skip empty rows
                if (empty($row) || !isset($row[0]) || trim($row[0]) === '') {
                    continue;
                }

                // Skip header row if present
                if ($header) {
                    $header = false;
                    $col1 = strtolower(trim($row[0]));
                    if ($col1 === 'city' || $col1 === 'city name' || $col1 === 'name') {
                        continue;
                    }
                }

                $cityName = trim($row[0]);
                $categoryName = isset($row[1]) ? strtolower(trim($row[1])) : 'micro';

                // Map category name to ID (default to Micro if not found)
                $categoryId = $categoriesMap[$categoryName] ?? ($categoriesMap['micro'] ?? null);

                if (!$categoryId) {
                    // Fallback to first available category
                    $categoryId = \App\Models\Category::value('id');
                }

                // Check if city name already exists
                if (City::where('name', $cityName)->exists()) {
                    $skippedCount++;
                    continue;
                }

                City::create([
                    'name' => $cityName,
                    'category_id' => $categoryId
                ]);

                $importedCount++;
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('admin.cities.index')
                ->with('success', "Bulk import completed! {$importedCount} cities imported, {$skippedCount} skipped (already existed).");

        } catch (\Exception $e) {
            fclose($handle);
            DB::rollBack();
            Log::error("Bulk Upload Error: " . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
