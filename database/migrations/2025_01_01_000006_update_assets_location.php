<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengganti FK asset_location_id (→ asset_locations)
 * dengan location_id (→ locations) yang lebih clean.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // 1. Hapus FK constraint lama terlebih dahulu
            $table->dropForeign(['asset_location_id']);
            // 2. Hapus kolom lama
            $table->dropColumn('asset_location_id');

            // 3. Tambah kolom & FK baru
            $table->foreignId('location_id')
                  ->nullable()
                  ->after('asset_category_id')
                  ->constrained('locations')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');

            $table->foreignId('asset_location_id')
                  ->nullable()
                  ->after('asset_category_id')
                  ->constrained('asset_locations')
                  ->nullOnDelete();
        });
    }
};
