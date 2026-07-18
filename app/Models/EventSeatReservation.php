<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSeatReservation extends Model
{
    use HasFactory;

    protected $table = 'event_seat_reservations';
    protected $primaryKey = 'id_event_seat_reservation';

    protected $fillable = [
        'id_payment_transaction',
        'id_course',
        'id_mahasiswa',
        'status',
        'expires_at',
        'confirmed_at',
        'released_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
