<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\ProductSoldNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\MeetingRequest;

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
        'is_available' => 'boolean',
    ];

    // Eager load common relationships by default
    protected $with = [];

    /**
     * Cache key untuk produk
     */
    public function getCacheKey(string $suffix = ''): string
    {
        return "produk.{$this->id}" . ($suffix ? ".{$suffix}" : '');
    }

    /**
     * Get cached product data
     */
    public static function getCached(int $id): ?self
    {
        return Cache::remember("produk.{$id}", 3600, function () use ($id) {
            return static::with(['gambarProduks', 'panoramaProduks', 'denahProduks'])
                ->find($id);
        });
    }

    /**
     * Get available products with caching
     */
    public static function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('produk.available', 1800, function () {
            return static::with(['gambarProduks'])
                ->where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Scope untuk produk yang tersedia
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope untuk kategori tertentu
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope untuk tipe tertentu
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk range harga
     */
    public function scopePriceRange(Builder $query, float $min = null, float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

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

    /**
     * Get meeting requests for this product
     */
    public function meetingRequests(): \Illuminate\Database\Eloquent\Collection
    {
        return MeetingRequest::where('produk_ids', 'like', '%"' . $this->id . '"%')
            ->with('user')
            ->get();
    }

    /**
     * Notify customers when product is sold
     */
    public function notifyCustomersIfSold(): void
    {
        // Hanya kirim notifikasi jika produk tidak tersedia (terjual)
        if (!$this->is_available) {
            $meetingRequests = MeetingRequest::getCustomersForProducts([$this->id]);

            foreach ($meetingRequests as $meetingRequest) {
                // Kirim notification ke customer
                $meetingRequest->user->notify(
                    new ProductSoldNotification($this, $meetingRequest)
                );

                // Mark sebagai sudah dinotifikasi
                $meetingRequest->markAsNotified();
            }
        }
    }

    /**
     * Clear cache when model is updated
     */
    protected static function booted(): void
    {
        static::saved(function ($produk) {
            // Clear caches
            Cache::forget($produk->getCacheKey());
            Cache::forget('produk.available');
            Cache::forget('produk.categories');
            Cache::forget('produk.types');

            // Check if product was marked as sold and notify customers
            if ($produk->wasChanged('is_available')) {
                $produk->notifyCustomersIfSold();
            }
        });

        static::deleted(function ($produk) {
            // Clear caches
            Cache::forget($produk->getCacheKey());
            Cache::forget('produk.available');
            Cache::forget('produk.categories');
            Cache::forget('produk.types');

            // Notify customers that product is no longer available
            $produk->notifyCustomersIfSold();
        });
    }

    /**
     * Get all available categories (cached)
     */
    public static function getCategories(): \Illuminate\Support\Collection
    {
        return Cache::remember('produk.categories', 7200, function () {
            return static::distinct('category')
                ->whereNotNull('category')
                ->pluck('category')
                ->filter()
                ->sort();
        });
    }

    /**
     * Get all available types (cached)
     */
    public static function getTypes(): \Illuminate\Support\Collection
    {
        return Cache::remember('produk.types', 7200, function () {
            return static::distinct('type')
                ->whereNotNull('type')
                ->pluck('type')
                ->filter()
                ->sort();
        });
    }
}
