<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Ruang Server',    'building' => 'Gedung A', 'floor' => 'Lantai 1'],
            ['name' => 'Ruang IT',        'building' => 'Gedung A', 'floor' => 'Lantai 2'],
            ['name' => 'Ruang Direksi',   'building' => 'Gedung A', 'floor' => 'Lantai 3'],
            ['name' => 'Kantor Pusat',    'building' => 'Gedung B', 'floor' => 'Lantai 1'],
            ['name' => 'Ruang Rapat A',   'building' => 'Gedung B', 'floor' => 'Lantai 2'],
            ['name' => 'Ruang Rapat B',   'building' => 'Gedung B', 'floor' => 'Lantai 2'],
            ['name' => 'Ruang HRD',       'building' => 'Gedung B', 'floor' => 'Lantai 3'],
            ['name' => 'Ruang Keuangan',  'building' => 'Gedung C', 'floor' => 'Lantai 1'],
            ['name' => 'Gudang Aset',     'building' => 'Gedung C', 'floor' => 'Lantai 1'],
            ['name' => 'Lobby Utama',     'building' => 'Gedung A', 'floor' => 'Lantai 1'],
        ];

        DB::table('asset_locations')->insertOrIgnore($locations);
    }
}
