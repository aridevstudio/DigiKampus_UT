<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    protected $table = 'forum_topics';
    protected $primaryKey = 'id_forum_topic';

    protected $fillable = [
        'category_id',
        'user_id',
        'judul',
        'slug',
        'isi',
        'views',
        'is_pinned',
        'is_locked',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'views' => 'integer',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ForumTopic $topic) {
            $topic->slug = static::generateUniqueSlug((string) ($topic->slug ?: $topic->judul));
            if (blank($topic->last_activity_at)) {
                $topic->last_activity_at = now();
            }
        });

        static::updating(function (ForumTopic $topic) {
            if ($topic->isDirty('judul') || ($topic->isDirty('slug') && blank($topic->slug))) {
                $topic->slug = static::generateUniqueSlug((string) ($topic->slug ?: $topic->judul), $topic->id_forum_topic);
            }
        });
    }

    public static function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = 'topik';
        }

        $slug = $base;
        $counter = 1;

        $query = static::query()->where('slug', $slug);
        if ($ignoreId !== null) {
            $query->where('id_forum_topic', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $counter++;
            $slug = $base . '-' . $counter;
            $query = static::query()->where('slug', $slug);
            if ($ignoreId !== null) {
                $query->where('id_forum_topic', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id', 'id_forum_category');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'topic_id', 'id_forum_topic');
    }

    public function publishedComments()
    {
        return $this->hasMany(ForumComment::class, 'topic_id', 'id_forum_topic')
            ->where('status', 'published');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['published', 'hidden']);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function touchActivity(): void
    {
        $this->forceFill(['last_activity_at' => now()])->save();
    }
}
