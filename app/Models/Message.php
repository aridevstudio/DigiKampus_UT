<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'id_message';

    protected $fillable = [
        'id_sender',
        'id_receiver',
        'content',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender user.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'id_sender', 'id');
    }

    /**
     * Get the receiver user.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'id_receiver', 'id');
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for conversation between two users.
     */
    public function scopeConversation($query, int $userId1, int $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('id_sender', $userId1)->where('id_receiver', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('id_sender', $userId2)->where('id_receiver', $userId1);
        });
    }
}
