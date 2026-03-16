<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
        'used_by_user_id',
        'used_payment_transaction_id',
        'used_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'is_active' => 'boolean',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->whereNull('used_at');
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
