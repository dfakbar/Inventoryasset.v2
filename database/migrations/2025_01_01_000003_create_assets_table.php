<?php

use App\Enums\AssetStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // Identitas
            $table->string('asset_code', 30)->unique()->comment('Auto-generate: ASTMON260301');
            $table->string('name', 200);

            // Relasi
            $table->foreignId('asset_category_id')
                  ->constrained('asset_categories')
                  ->restrictOnDelete();

            $table->foreignId('asset_location_id')
                  ->nullable()
                  ->constrained('asset_locations')
                  ->nullOnDelete();

            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User yang menggunakan aset ini');

            // Spesifikasi Fisik
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 150)->nullable()->unique();

            // Finansial & Pengadaan
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);

            // Status & Kondisi
            $table->string('status', 30)->default(AssetStatus::Spare->value);

            // Tambahan
            $table->text('notes')->nullable();
            $table->string('image', 300)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // === Indexes untuk performa query ===
            $table->index('status');
            $table->index('asset_category_id');
            $table->index(['asset_category_id', 'status'], 'idx_assets_category_status');
            $table->index('purchase_date');
            $table->index('asset_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
