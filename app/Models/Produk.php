<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}