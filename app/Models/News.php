<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $primaryKey = 'id_news';

    protected $fillable = [
        'judul',
        'konten',
        'thumbnail',
        'kategori',
        'target_prodi',
        'tanggal_publish',
        'is_active'
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope active news only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope published news only.
     */
    public function scopePublished($query)
    {
        return $query->where('tanggal_publish', '<=', now());
    }

    /**
     * Scope news visible for a specific prodi target.
     */
    public function scopeVisibleForProdi($query, array $targetKeys)
    {
        return $query->where(function ($q) use ($targetKeys) {
            $q->whereNull('target_prodi')
                ->orWhere('target_prodi', '')
                ->orWhere('target_prodi', 'all');

            if (!empty($targetKeys)) {
                $q->orWhereIn('target_prodi', $targetKeys);
            }
        });
    }

    /**
     * Get relative time for display.
     */
    public function getWaktuRelatifAttribute()
    {
        return $this->tanggal_publish->diffForHumans();
    }
}
