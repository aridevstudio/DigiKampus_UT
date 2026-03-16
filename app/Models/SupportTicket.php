<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';
    protected $primaryKey = 'id_support_ticket';

    protected $fillable = [
        'id_mahasiswa',
        'answered_by',
        'subject',
        'question',
        'faq_suggestion',
        'admin_reply',
        'status',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by', 'id');
    }
}
