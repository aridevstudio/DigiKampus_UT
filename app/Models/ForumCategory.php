<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $table = 'forum_categories';
    protected $primaryKey = 'id_forum_category';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
        'warna',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ForumCategory $category) {
            $category->slug = static::generateUniqueSlug((string) ($category->slug ?: $category->nama));
        });

        static::updating(function (ForumCategory $category) {
            if ($category->isDirty('nama') || ($category->isDirty('slug') && blank($category->slug))) {
                $category->slug = static::generateUniqueSlug((string) ($category->slug ?: $category->nama), $category->id_forum_category);
            }
        });
    }

    public static function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'kategori';
        }

        $slug = $base;
        $counter = 1;

        $query = static::query()->where('slug', $slug);
        if ($ignoreId !== null) {
            $query->where('id_forum_category', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $counter++;
            $slug = $base . '-' . $counter;
            $query = static::query()->where('slug', $slug);
            if ($ignoreId !== null) {
                $query->where('id_forum_category', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'category_id', 'id_forum_category');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }
}
