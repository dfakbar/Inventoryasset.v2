<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Monitor',      'abbreviation' => 'MON', 'description' => 'Monitor / Layar komputer'],
            ['name' => 'Laptop',       'abbreviation' => 'LPT', 'description' => 'Laptop / Notebook'],
            ['name' => 'Komputer PC',  'abbreviation' => 'KPC', 'description' => 'Personal Computer desktop'],
            ['name' => 'Printer',      'abbreviation' => 'PRT', 'description' => 'Printer / Pencetak dokumen'],
            ['name' => 'Proyektor',    'abbreviation' => 'PRY', 'description' => 'Proyektor presentasi'],
            ['name' => 'Server',       'abbreviation' => 'SVR', 'description' => 'Server fisik'],
            ['name' => 'Router',       'abbreviation' => 'RTR', 'description' => 'Router jaringan'],
            ['name' => 'Switch',       'abbreviation' => 'SWT', 'description' => 'Network switch'],
            ['name' => 'Scanner',      'abbreviation' => 'SCN', 'description' => 'Scanner dokumen'],
            ['name' => 'UPS',          'abbreviation' => 'UPS', 'description' => 'Uninterruptible Power Supply'],
            ['name' => 'Telepon',      'abbreviation' => 'TEL', 'description' => 'Telepon kantor / VOIP'],
            ['name' => 'Kamera CCTV',  'abbreviation' => 'CCV', 'description' => 'Kamera pengawas CCTV'],
            ['name' => 'Meja',         'abbreviation' => 'MJA', 'description' => 'Meja kerja / konferensi'],
            ['name' => 'Kursi',        'abbreviation' => 'KRS', 'description' => 'Kursi kantor'],
            ['name' => 'Lainnya',      'abbreviation' => 'LAN', 'description' => 'Aset lain-lain'],
        ];

        DB::table('asset_categories')->insertOrIgnore($categories);
    }
}
