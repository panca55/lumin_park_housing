<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    // Minimal fillable fields for katalog/produk
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'type',
        'image',
        'model_3d',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function gambarProduks(): HasMany
    {
        return $this->hasMany(GambarProduk::class);
    }

    public function panoramaProduks(): HasMany
    {
        return $this->hasMany(PanoramaProduk::class);
    }

    public function denahProduks(): HasMany
    {
        return $this->hasMany(Denah::class);
    }
}
