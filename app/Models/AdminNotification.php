<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'admin_id',
        'judul',
        'konten',
        'tipe',
        'icon',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Create a notification for all admins.
     */
    public static function notifyAllAdmins(string $judul, ?string $konten = null, string $tipe = 'info', ?string $icon = null, ?string $link = null): void
    {
        $admins = User::where('role', 'admin')->where('status', 'aktif')->pluck('id');

        $records = $admins->map(fn($adminId) => [
            'admin_id' => $adminId,
            'judul' => $judul,
            'konten' => $konten,
            'tipe' => $tipe,
            'icon' => $icon,
            'link' => $link,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        static::insert($records);
    }
}
