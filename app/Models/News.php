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
     * Get relative time for display.
     */
    public function getWaktuRelatifAttribute()
    {
        return $this->tanggal_publish->diffForHumans();
    }
}
