<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // URUTAN PENTING: Permission harus ada sebelum user di-assign role/permission
        $this->call([
            PermissionSeeder::class,    // 1. Buat roles & permissions
            AdminUserSeeder::class,     // 2. Buat user admin + staff + agent
            AssetCategorySeeder::class,
            BrandSeeder::class,
            AssetLocationSeeder::class,
            LocationSeeder::class,
            SlaPolicySeeder::class,     // 8. Buat default SLA policies
            TicketCategorySeeder::class, // 9. Buat kategori tiket default
        ]);
    }
}
