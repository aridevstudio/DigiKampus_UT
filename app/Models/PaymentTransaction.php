<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

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
}
