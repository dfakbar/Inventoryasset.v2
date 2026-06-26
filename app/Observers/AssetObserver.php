<?php

namespace App\Observers;

use App\Models\Asset;
use App\Services\AssetCodeGenerator;
use Illuminate\Support\Facades\Log;

/**
 * Observer yang secara otomatis men-generate kode aset unik
 * setiap kali aset baru akan disimpan ke database.
 *
 * Didaftarkan di AppServiceProvider::boot().
 * PENTING: Asset::create() harus dipanggil di dalam DB::transaction()
 *          agar lockForUpdate() di AssetCodeGenerator bekerja dengan benar.
 */
class AssetObserver
{
    public function __construct(
        private readonly AssetCodeGenerator $codeGenerator
    ) {}

    /**
     * Event "creating" dipicu sebelum INSERT ke database.
     * Di sinilah kode aset di-generate secara otomatis.
     */
    public function creating(Asset $asset): void
    {
        // Jika kode sudah di-set secara manual, jangan override.
        if (!empty($asset->asset_code)) {
            return;
        }

        // Lazy-load relasi category menggunakan foreign key yang sudah di-set.
        $category = $asset->category;

        if ($category === null) {
            // Ini seharusnya tidak terjadi karena validasi sudah menjaganya.
            Log::error('AssetObserver: Gagal generate kode - kategori tidak ditemukan.', [
                'asset_category_id' => $asset->asset_category_id,
            ]);
            throw new \RuntimeException('Kategori aset tidak ditemukan. Kode tidak dapat di-generate.');
        }

        $asset->asset_code = $this->codeGenerator->generate($category, now());

        Log::info("AssetObserver: Kode aset {$asset->asset_code} berhasil di-generate.", [
            'category' => $category->name,
        ]);
    }
}
