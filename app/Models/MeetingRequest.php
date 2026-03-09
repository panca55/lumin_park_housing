<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MeetingRequest extends Model
{
    protected $fillable = [
        'user_id',
        'produk_ids',
        'tanggal_meeting',
        'jam_meeting',
        'status',
        'whatsapp_message',
        'notified_if_sold',
        'notification_read_at',
        'sold_product_ids'
    ];

    protected $casts = [
        'produk_ids' => 'array',
        'tanggal_meeting' => 'date',
        'jam_meeting' => 'datetime:H:i',
        'notified_if_sold' => 'boolean',
        'notification_read_at' => 'datetime',
        'sold_product_ids' => 'array'
    ];

    /**
     * Get the user who requested the meeting
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get products related to this meeting request
     */
    public function produks(): BelongsToMany
    {
        return $this->belongsToMany(
            Produk::class,
            'meeting_request_produk',
            'meeting_request_id',
            'produk_id'
        );
    }

    /**
     * Check if this meeting request includes specific product
     */
    public function includesProduct(int $produkId): bool
    {
        return in_array($produkId, $this->produk_ids ?? []);
    }

    /**
     * Get customers who have meeting requests for specific product(s)
     */
    public static function getCustomersForProducts(array $produkIds): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['user'])
            ->where(function ($query) use ($produkIds) {
                foreach ($produkIds as $produkId) {
                    $query->orWhereJsonContains('produk_ids', $produkId);
                }
            })
            ->where('notified_if_sold', false)
            ->where('status', '!=', 'cancelled')
            ->get();
    }

    /**
     * Mark as notified for sold product
     */
    public function markAsNotified(): void
    {
        $this->update(['notified_if_sold' => true]);
    }

    /**
     * Mark specific product as sold in this meeting request
     */
    public function markProductAsSold(int $produkId): void
    {
        // Mark as notified if this request includes the sold product
        if ($this->includesProduct($produkId)) {
            $soldProducts = $this->sold_product_ids ?? [];

            if (!in_array($produkId, $soldProducts)) {
                $soldProducts[] = $produkId;
            }

            $this->update([
                'notified_if_sold' => true,
                'sold_product_ids' => $soldProducts
            ]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(): void
    {
        $this->update([
            'notification_read_at' => now()
        ]);
    }

    /**
     * Check if notification has been read
     */
    public function isNotificationRead(): bool
    {
        return $this->notification_read_at !== null;
    }

    /**
     * Scope untuk meeting requests yang belum dinotifikasi
     */
    public function scopeNotNotified($query)
    {
        return $query->where('notified_if_sold', false);
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get sold product notifications for a user
     */
    public static function getSoldProductNotifications(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['user'])
            ->where('user_id', $userId)
            ->where('notified_if_sold', true)
            ->where('status', '!=', 'cancelled')
            ->whereNull('notification_read_at') // Hanya yang belum dibaca
            ->orderBy('updated_at', 'desc')
            ->get()
            ->filter(function ($meetingRequest) {
                // Filter hanya yang produknya benar-benar sudah terjual
                $soldProductIds = $meetingRequest->sold_product_ids ?? [];
                if (empty($soldProductIds)) {
                    return false;
                }

                $soldProducts = Produk::whereIn('id', $soldProductIds)
                    ->where('is_sold', true)
                    ->count();

                return $soldProducts > 0;
            });
    }

    /**
     * Get products that are sold from this meeting request
     */
    public function getSoldProducts(): \Illuminate\Database\Eloquent\Collection
    {
        $soldProductIds = $this->sold_product_ids ?? [];

        if (empty($soldProductIds)) {
            return collect([]);
        }

        return Produk::whereIn('id', $soldProductIds)
            ->where('is_sold', true)
            ->get();
    }

    /**
     * Boot method untuk handle event
     */
    protected static function boot()
    {
        parent::boot();

        // Refresh cache saat meeting request baru dibuat
        static::created(function ($meetingRequest) {
            $meetingRequest->refreshProductBookingCache();
        });

        // Refresh cache saat meeting request diupdate (status berubah)
        static::updated(function ($meetingRequest) {
            $meetingRequest->refreshProductBookingCache();
        });

        // Refresh cache saat meeting request dihapus
        static::deleted(function ($meetingRequest) {
            $meetingRequest->refreshProductBookingCache();
        });
    }

    /**
     * Refresh booking cache untuk produk-produk terkait
     */
    private function refreshProductBookingCache(): void
    {
        if (!empty($this->produk_ids)) {
            foreach ($this->produk_ids as $produkId) {
                $produk = Produk::find($produkId);
                if ($produk) {
                    $produk->refreshBookingCountCache();
                }
            }
        }
    }
}
