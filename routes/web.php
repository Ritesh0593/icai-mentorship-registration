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
    Route::get('/cities/{city}/download-qr', [CityController::class, 'downloadQrCode'])->name('admin.cities.download-qr');

    // Registrations Management
    Route::get('/registrations', [DashboardController::class, 'registrations'])->name('admin.registrations.index');
    Route::get('/registrations/export', [DashboardController::class, 'export'])->name('admin.registrations.export');
});
