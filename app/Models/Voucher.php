<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';
    protected $primaryKey = 'id_voucher';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_subtotal',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
        'used_by_user_id',
        'used_payment_transaction_id',
        'used_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeAvailable($query)
    {
        if (
            !Schema::hasColumn('vouchers', 'usage_limit')
            || !Schema::hasColumn('vouchers', 'used_count')
            || !Schema::hasColumn('vouchers', 'expires_at')
        ) {
            return $query->where('is_active', true)->whereNull('used_at');
        }

        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    public static function releaseExpiredReservations(): void
    {
        if (
            !Schema::hasTable('voucher_usages')
            || !Schema::hasTable('payment_transactions')
            || !Schema::hasColumn('vouchers', 'used_count')
        ) {
            return;
        }

        $expiredUsageIds = VoucherUsage::query()
            ->where('status', 'reserved')
            ->whereNull('released_at')
            ->whereHas('paymentTransaction', function ($query) {
                $query->where('transaction_status', 'pending')
                    ->where('created_at', '<', PaymentTransaction::pendingExpiryCutoff());
            })
            ->pluck('id_voucher_usage');

        if ($expiredUsageIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($expiredUsageIds) {
            $usages = VoucherUsage::whereIn('id_voucher_usage', $expiredUsageIds)
                ->whereNull('released_at')
                ->lockForUpdate()
                ->get();

            foreach ($usages as $usage) {
                $usage->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);

                $voucher = self::whereKey($usage->id_voucher)->lockForUpdate()->first();
                if ($voucher) {
                    $voucher->forceFill([
                        'used_count' => max(0, (int) $voucher->used_count - 1),
                    ])->save();
                }
            }
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasUsageRemaining(): bool
    {
        return $this->usage_limit === null || (int) $this->used_count < (int) $this->usage_limit;
    }

    public function isAvailableForCheckout(): bool
    {
        return (bool) $this->is_active && !$this->isExpired() && $this->hasUsageRemaining();
    }

    public function remainingUses(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }

        return max(0, (int) $this->usage_limit - (int) $this->used_count);
    }

    public function usageLabel(): string
    {
        if ($this->usage_limit === null) {
            return (int) $this->used_count . ' digunakan / tanpa batas';
        }

        return (int) $this->used_count . ' / ' . (int) $this->usage_limit . ' digunakan';
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class, 'id_voucher', 'id_voucher');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_user_id', 'id');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'used_payment_transaction_id', 'id_payment_transaction');
    }
}
