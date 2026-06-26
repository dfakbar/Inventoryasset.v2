<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetLocation extends Model
{
    protected $fillable = [
        'name',
        'building',
        'floor',
    ];

    // =========================================================
    // Accessors
    // =========================================================

    /**
     * Alamat lengkap lokasi: "Gedung A - Lt. 2 - Ruang IT"
     */
    public function getFullAddressAttribute(): string
    {
        return implode(' › ', array_filter([
            $this->building,
            $this->floor,
            $this->name,
        ]));
    }

    // =========================================================
    // Relations
    // =========================================================

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
