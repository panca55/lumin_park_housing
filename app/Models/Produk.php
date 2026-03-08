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
     * Get meeting requests that include this product
     */
    public function meetingRequests()
    {
        return MeetingRequest::where(function ($query) {
            $query->whereJsonContains('produk_ids', $this->id);
        });
    }

    /**
     * Get meeting requests collection for this product
     */
    public function getMeetingRequestsCollection(): \Illuminate\Database\Eloquent\Collection
    {
        return MeetingRequest::where(function ($query) {
            $query->whereJsonContains('produk_ids', $this->id);
        })
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

    // ===========================
    // MEETING & BOOKING ANALYTICS
    // ===========================

    /**
     * Get total booking count for this product
     */
    public function getBookingCount(): int
    {
        return Cache::remember($this->getCacheKey('booking_count'), 1800, function () {
            return MeetingRequest::where('status', '!=', 'cancelled')
                ->where(function ($query) {
                    $query->whereJsonContains('produk_ids', $this->id);
                })
                ->count();
        });
    }

    /**
     * Get pending booking count (not confirmed/completed)
     */
    public function getPendingBookingCount(): int
    {
        return MeetingRequest::whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) {
                $query->whereJsonContains('produk_ids', $this->id);
            })
            ->count();
    }

    /**
     * Get booking trend (last 30 days)
     */
    public function getBookingTrend(): array
    {
        return Cache::remember($this->getCacheKey('booking_trend'), 3600, function () {
            $bookings = MeetingRequest::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', now()->subDays(30))
                ->where(function ($query) {
                    $query->whereJsonContains('produk_ids', $this->id);
                })
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date')
                ->map(fn($item) => $item->count)
                ->toArray();

            // Fill missing dates with 0
            $result = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $result[$date] = $bookings[$date] ?? 0;
            }

            return $result;
        });
    }

    /**
     * Update booking count cache when new meeting request is created
     */
    public function refreshBookingCountCache(): void
    {
        Cache::forget($this->getCacheKey('booking_count'));
        Cache::forget($this->getCacheKey('booking_trend'));

        // Re-cache immediately
        $this->getBookingCount();
        $this->getBookingTrend();
    }

    /**
     * Get most booked products (static method for analytics)
     */
    public static function getMostBookedProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('products.most_booked', 3600, function () use ($limit) {
            // Get booking counts for all products
            $bookingCounts = [];

            static::chunk(50, function ($products) use (&$bookingCounts) {
                foreach ($products as $product) {
                    $count = MeetingRequest::where('status', '!=', 'cancelled')
                        ->where(function ($query) use ($product) {
                            $query->whereJsonContains('produk_ids', $product->id);
                        })
                        ->count();

                    if ($count > 0) {
                        $bookingCounts[$product->id] = $count;
                    }
                }
            });

            // Sort by booking count and get top products
            arsort($bookingCounts);
            $topProductIds = array_slice(array_keys($bookingCounts), 0, $limit, true);

            return static::whereIn('id', $topProductIds)
                ->get()
                ->sortBy(function ($product) use ($bookingCounts) {
                    return - ($bookingCounts[$product->id] ?? 0);
                });
        });
    }

    /**
     * Get booking analytics summary
     */
    public function getBookingAnalytics(): array
    {
        $total = $this->getBookingCount();
        $pending = $this->getPendingBookingCount();
        $completed = MeetingRequest::where('status', 'completed')
            ->where(function ($query) {
                $query->whereJsonContains('produk_ids', $this->id);
            })
            ->count();

        $trend = $this->getBookingTrend();
        $recentBookings = array_slice($trend, -7, 7, true); // Last 7 days

        return [
            'total_bookings' => $total,
            'pending_bookings' => $pending,
            'completed_bookings' => $completed,
            'recent_trend' => $recentBookings,
            'average_per_day' => $total > 0 ? round($total / max(1, count(array_filter($trend))), 2) : 0,
            'popularity_score' => $this->calculatePopularityScore(),
        ];
    }

    /**
     * Calculate popularity score based on various factors
     */
    private function calculatePopularityScore(): float
    {
        $bookingCount = $this->getBookingCount();
        $recentBookings = array_sum(array_slice($this->getBookingTrend(), -7, 7));
        $daysListed = max(1, now()->diffInDays($this->created_at));

        // Weight: 40% total bookings, 40% recent activity, 20% time factor
        $score = ($bookingCount * 0.4) +
            ($recentBookings * 0.4) +
            (min(10, $bookingCount / max(1, $daysListed / 30)) * 0.2);

        return round($score, 2);
    }
}
