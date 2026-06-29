<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Auth routes (login, logout, reset password — register dinonaktifkan)
require __DIR__.'/auth.php';

Route::get('/', fn () => redirect()->route('assets.index'));

// ╔══════════════════════════════════════════════════════════════╗
// ║  AUTHENTICATED ROUTES                                        ║
// ╚══════════════════════════════════════════════════════════════╝
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ── Aset (akses dikontrol per-permission di controller) ──────
    Route::resource('assets', AssetController::class);

    // ── Kategori, Merek, Vendor & Lokasi (akses dikontrol per-permission di controller) ────
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('locations', LocationController::class);
    });

    // ── User Management (Super Admin only) ──────────────────────
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserController::class);
        });
});
