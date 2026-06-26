<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('building', 100)->nullable()->comment('Gedung/Bangunan');
            $table->string('floor', 50)->nullable()->comment('Lantai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_locations');
    }
};
