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
        'notified_if_sold'
    ];

    protected $casts = [
        'produk_ids' => 'array',
        'tanggal_meeting' => 'date',
        'jam_meeting' => 'datetime:H:i',
        'notified_if_sold' => 'boolean'
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
