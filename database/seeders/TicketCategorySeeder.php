<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hardware',       'slug' => 'hardware',       'description' => 'Masalah perangkat keras komputer, laptop, printer, dll'],
            ['name' => 'Software',       'slug' => 'software',       'description' => 'Masalah aplikasi, sistem operasi, lisensi'],
            ['name' => 'Jaringan',       'slug' => 'jaringan',       'description' => 'Masalah koneksi internet, LAN, WiFi, VPN'],
            ['name' => 'Email & Akun',   'slug' => 'email-akun',     'description' => 'Permintaan pembuatan/reset akun, email, password'],
            ['name' => 'Permintaan Akses', 'slug' => 'permintaan-akses', 'description' => 'Permintaan akses ke sistem/aplikasi/direktori'],
            ['name' => 'Lainnya',        'slug' => 'lainnya',         'description' => 'Permintaan atau masalah yang tidak termasuk kategori lain'],
        ];

        foreach ($categories as $cat) {
            TicketCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // Sub-kategori Hardware
        $hardware = TicketCategory::where('slug', 'hardware')->first();
        if ($hardware) {
            $subs = [
                ['name' => 'Laptop/PC',    'slug' => 'laptop-pc',    'description' => 'Masalah laptop atau komputer desktop'],
                ['name' => 'Printer',      'slug' => 'printer',      'description' => 'Masalah printer, scanner, multifunction'],
                ['name' => 'Perangkat Lain', 'slug' => 'perangkat-lain', 'description' => 'Aksesoris, monitor, mouse, keyboard, dll'],
            ];
            foreach ($subs as $sub) {
                TicketCategory::firstOrCreate(
                    ['slug' => $sub['slug']],
                    [
                        'name' => $sub['name'],
                        'parent_id' => $hardware->id,
                        'slug' => $sub['slug'],
                        'description' => $sub['description'],
                    ]
                );
            }
        }

        // Sub-kategori Software
        $software = TicketCategory::where('slug', 'software')->first();
        if ($software) {
            $subs = [
                ['name' => 'ERP System',   'slug' => 'erp-system',   'description' => 'Masalah terkait sistem ERP perusahaan'],
                ['name' => 'Microsoft Office', 'slug' => 'microsoft-office', 'description' => 'Masalah Word, Excel, Outlook, Teams, dll'],
                ['name' => 'Sistem Operasi', 'slug' => 'sistem-operasi', 'description' => 'Masalah Windows, Linux, macOS'],
            ];
            foreach ($subs as $sub) {
                TicketCategory::firstOrCreate(
                    ['slug' => $sub['slug']],
                    [
                        'name' => $sub['name'],
                        'parent_id' => $software->id,
                        'slug' => $sub['slug'],
                        'description' => $sub['description'],
                    ]
                );
            }
        }
    }
}
