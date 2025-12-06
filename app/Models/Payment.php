<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'amount',
        'payment_method',
        'payment_proof',
        'status',
        'admin_notes',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => Carbon::now()
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes
        ]);
    }
}
