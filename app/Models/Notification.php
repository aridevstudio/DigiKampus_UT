<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';

    protected $fillable = [
        'id_mahasiswa',
        'judul',
        'konten',
        'tipe',
        'icon',
        'icon_color',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the mahasiswa that owns the notification.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope by tipe.
     */
    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    /**
     * Get waktu relatif.
     */
    public function getWaktuRelatifAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public static function notifyMahasiswa(
        int $mahasiswaId,
        string $judul,
        ?string $konten = null,
        string $tipe = 'umum',
        ?string $icon = null,
        string $iconColor = '#3B82F6'
    ): self {
        return self::create([
            'id_mahasiswa' => $mahasiswaId,
            'judul' => $judul,
            'konten' => $konten,
            'tipe' => $tipe,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'is_read' => false,
        ]);
    }
}
