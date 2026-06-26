<?php

namespace App\Providers;

use App\Models\Asset;
use App\Observers\AssetObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan observer untuk auto-generate kode aset
        Asset::observe(AssetObserver::class);

        // Gunakan Bootstrap 5 untuk tampilan pagination Laravel
        Paginator::useBootstrapFive();
    }
}
