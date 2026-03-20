<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    public const PENDING_EXPIRY_HOURS = 24;

    protected $table = 'payment_transactions';
    protected $primaryKey = 'id_payment_transaction';

    protected $fillable = [
        'order_id',
        'id_mahasiswa',
        'id_voucher',
        'payment_provider',
        'preferred_payment_method',
        'payment_method',
        'subtotal_amount',
        'service_fee',
        'discount_amount',
        'gross_amount',
        'transaction_status',
        'fraud_status',
        'snap_token',
        'snap_redirect_url',
        'paid_at',
        'payload',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'payload' => 'array',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'id_voucher', 'id_voucher');
    }

    public function items()
    {
        return $this->hasMany(PaymentTransactionItem::class, 'id_payment_transaction', 'id_payment_transaction');
    }

    public static function pendingExpiryCutoff(): Carbon
    {
        return now()->subHours(self::PENDING_EXPIRY_HOURS);
    }

    public function getPendingExpiresAtAttribute(): ?Carbon
    {
        if (!$this->created_at) {
            return null;
        }

        return $this->created_at->copy()->addHours(self::PENDING_EXPIRY_HOURS);
    }

    public function isExpiredPending(): bool
    {
        return $this->transaction_status === 'pending'
            && $this->pending_expires_at instanceof Carbon
            && now()->greaterThan($this->pending_expires_at);
    }

    public function getEffectiveTransactionStatusAttribute(): string
    {
        if ($this->isExpiredPending()) {
            return 'expire';
        }

        return (string) $this->transaction_status;
    }

    public function getEffectiveStatusLabelAttribute(): string
    {
        return match ($this->effective_transaction_status) {
            'settlement', 'capture' => 'Berhasil',
            'pending' => 'Menunggu',
            default => 'Gagal',
        };
    }
}
