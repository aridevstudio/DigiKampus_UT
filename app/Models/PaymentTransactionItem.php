<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransactionItem extends Model
{
    use HasFactory;

    protected $table = 'payment_transaction_items';
    protected $primaryKey = 'id_payment_transaction_item';

    protected $fillable = [
        'id_payment_transaction',
        'id_course',
        'course_name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'id_payment_transaction', 'id_payment_transaction');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}
