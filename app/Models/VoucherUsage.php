<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    use HasFactory;

    protected $table = 'voucher_usages';
    protected $primaryKey = 'id_voucher_usage';

    protected $fillable = [
        'id_voucher',
        'id_payment_transaction',
        'id_user',
        'discount_amount',
        'status',
        'used_at',
        'confirmed_at',
        'released_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'id_voucher', 'id_voucher');
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'id_payment_transaction', 'id_payment_transaction');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
