<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Ruang Server',   'department' => 'IT',          'description' => 'Ruang server utama perusahaan'],
            ['name' => 'Ruang IT',       'department' => 'IT',          'description' => 'Ruang tim teknologi informasi'],
            ['name' => 'Ruang Direksi',  'department' => 'Manajemen',   'description' => 'Ruang direktur dan manajemen senior'],
            ['name' => 'Kantor Pusat',   'department' => 'Umum',        'description' => 'Lantai utama kantor pusat'],
            ['name' => 'Ruang Rapat A',  'department' => 'Umum',        'description' => 'Ruang rapat utama kapasitas 20 orang'],
            ['name' => 'Ruang Rapat B',  'department' => 'Umum',        'description' => 'Ruang rapat kecil kapasitas 8 orang'],
            ['name' => 'Ruang HRD',      'department' => 'HRD',         'description' => 'Ruang departemen Human Resources'],
            ['name' => 'Ruang Keuangan', 'department' => 'Keuangan',    'description' => 'Ruang departemen keuangan & akuntansi'],
            ['name' => 'Gudang Aset',    'department' => 'Operasional', 'description' => 'Gudang penyimpanan aset cadangan'],
            ['name' => 'Lobby Utama',    'department' => 'Umum',        'description' => 'Area lobby dan resepsionis'],
        ];

        foreach ($locations as $data) {
            // Slug di-generate manual karena WithoutModelEvents menonaktifkan boot observer
            $data['slug'] = Str::slug($data['name']);

            Location::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
