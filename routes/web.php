<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ServiceDesk\CommentController;
use App\Http\Controllers\ServiceDesk\ReportController;
use App\Http\Controllers\ServiceDesk\SlaPolicyController;
use App\Http\Controllers\ServiceDesk\TicketCategoryController;
use App\Http\Controllers\ServiceDesk\TicketController;
use Illuminate\Support\Facades\Route;

// Auth routes (login, logout, reset password — register dinonaktifkan)
require __DIR__.'/auth.php';

Route::get('/', fn () => redirect()->route('dashboard'));

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

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SERVICE DESK ROUTES                                        ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::prefix('service-desk')->name('sd.')->group(function () {
        Route::get('dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');

        // Tickets — semua user authenticated bisa akses sesuai permission
        Route::get('tickets/status', [TicketController::class, 'statusView'])->name('tickets.status');
        Route::resource('tickets', TicketController::class)->except(['edit', 'update']);
        Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])
            ->middleware('can:ticket.manage')
            ->name('tickets.edit');
        Route::put('tickets/{ticket}', [TicketController::class, 'update'])
            ->middleware('can:ticket.manage')
            ->name('tickets.update');
        Route::post('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
            ->middleware('can:ticket.manage')
            ->name('tickets.status');
        Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])
            ->middleware('can:ticket.manage')
            ->name('tickets.assign');
        Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])
            ->middleware('can:ticket.delete')
            ->name('tickets.destroy');

        // Comments
        Route::post('tickets/{ticket}/comments', [CommentController::class, 'store'])->name('tickets.comments.store');
        Route::delete('tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy'])
            ->middleware('can:ticket.manage')
            ->name('tickets.comments.destroy');

        // SLA Policies — admin only
        Route::resource('sla-policies', SlaPolicyController::class)
            ->except(['show'])
            ->middleware('can:ticket.reports');

        // Ticket Categories — manage permission
        Route::resource('categories', TicketCategoryController::class)
            ->except(['show'])
            ->middleware('can:ticket.manage');

        // Reports — reports permission
        Route::get('reports', [ReportController::class, 'index'])
            ->middleware('can:ticket.reports')
            ->name('reports.index');
        Route::get('reports/agent-performance', [ReportController::class, 'agentPerformance'])
            ->middleware('can:ticket.reports')
            ->name('reports.agent');
    });
});
