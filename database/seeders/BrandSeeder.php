<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Dell', 'HP', 'Lenovo', 'Apple', 'ASUS', 'Acer',
            'Samsung', 'Epson', 'Canon', 'Cisco', 'TP-Link',
            'MikroTik', 'APC', 'Sony', 'Panasonic', 'Hikvision',
            'Dahua', 'Logitech', 'Kingston', 'Seagate', 'WD',
            'Intel', 'AMD', 'Microsoft', 'Xerox', 'Brother',
            'Fujitsu', 'Toshiba', 'Huawei', 'ZTE',
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate(['name' => $name]);
        }
    }
}
