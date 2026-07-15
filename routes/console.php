<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pengecekan SLA breach setiap 5 menit
// Tambahkan ke scheduler di server: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
Artisan::command('sd:check-sla', function () {
    $this->call(\App\Console\Commands\CheckSlaBreach::class);
})->describe('Periksa tiket yang melanggar SLA dan lakukan eskalasi');

// Schedule untuk production
// app()->environment('production') && schedule()->command('sd:check-sla')->everyFiveMinutes();
