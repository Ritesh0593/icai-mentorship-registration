<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Public\RegistrationController;
use Illuminate\Support\Facades\Route;

// Redirect homepage to admin dashboard (which redirects to login if unauthorized)
Route::redirect('/', '/admin');

// Public Registration Routes
Route::get('/register/success', [RegistrationController::class, 'success'])->name('registration.success');
Route::post('/register/send-otp', [RegistrationController::class, 'sendOtp'])->name('registration.send-otp');
Route::post('/register/verify-otp', [RegistrationController::class, 'verifyOtp'])->name('registration.verify-otp');
Route::get('/register/{slug}', [RegistrationController::class, 'show'])->name('registration.show');
Route::post('/register/{slug}', [RegistrationController::class, 'store'])->name('registration.store');

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard Routes (Protected by custom 'admin.auth' middleware)
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // City Management
    Route::get('/cities', [CityController::class, 'index'])->name('admin.cities.index');
    Route::post('/cities', [CityController::class, 'store'])->name('admin.cities.store');
    Route::get('/cities/export', [CityController::class, 'exportCsv'])->name('admin.cities.export');
    Route::get('/cities/download-all-qr', [CityController::class, 'downloadAllQrCodes'])->name('admin.cities.download-all-qr');
    Route::post('/cities/bulk-upload', [CityController::class, 'bulkUpload'])->name('admin.cities.bulk-upload');
    Route::get('/cities/{city}/download-qr', [CityController::class, 'downloadQrCode'])->name('admin.cities.download-qr');

    // Registrations Management
    Route::get('/registrations', [DashboardController::class, 'registrations'])->name('admin.registrations.index');
    Route::get('/registrations/export', [DashboardController::class, 'export'])->name('admin.registrations.export');
});

// Cache Clearing Utility Route (for server testing and config clears)
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    return 'Cache, config, and views cleared successfully!';
});

// Database Migration & Seeding Utility Route (For environments without SSH access)
Route::get('/db-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);
        return 'Database tables fresh-migrated and seeded successfully!';
    } catch (\Exception $e) {
        return 'Migration Error: ' . $e->getMessage();
    }
});

// Storage Link Utility Route (For environments without SSH access)
Route::get('/storage-link', function () {
    try {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // 1. If target directory doesn't exist, create it
        if (!file_exists($target)) {
            mkdir($target, 0755, true);
        }

        // 2. If link already exists (even if broken), delete it
        if (file_exists($link) || is_link($link)) {
            // Check if it is a link
            if (is_link($link)) {
                unlink($link);
            } else {
                // If it is a real directory (not a link), delete it recursively or rename it
                // To be safe and simple, let's rename it to storage_old
                rename($link, public_path('storage_old_' . time()));
            }
        }

        // 3. Create symlink
        if (symlink($target, $link)) {
            return 'Storage link created successfully!';
        }

        return 'Failed to create storage link.';
    } catch (\Exception $e) {
        return 'Storage Link Error: ' . $e->getMessage();
    }
});
