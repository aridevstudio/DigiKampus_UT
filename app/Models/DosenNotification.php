<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenNotification extends Model
{
    use HasFactory;

    protected $table = 'dosen_notifications';

    protected $fillable = [
        'dosen_id',
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

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Create a notification for a specific dosen.
     */
    public static function notifyDosen(int $dosenId, string $judul, ?string $konten = null, string $tipe = 'info', ?string $icon = null, ?string $link = null): self
    {
        return static::create([
            'dosen_id' => $dosenId,
            'judul' => $judul,
            'konten' => $konten,
            'tipe' => $tipe,
            'icon' => $icon,
            'link' => $link,
        ]);
    }

    /**
     * Create a notification for all active dosen.
     */
    public static function notifyAllDosen(string $judul, ?string $konten = null, string $tipe = 'info', ?string $icon = null, ?string $link = null): void
    {
        $dosens = User::where('role', 'dosen')->where('status', 'aktif')->pluck('id');

        $records = $dosens->map(fn($dosenId) => [
            'dosen_id' => $dosenId,
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
