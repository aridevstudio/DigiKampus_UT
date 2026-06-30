<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $table = 'forum_comments';
    protected $primaryKey = 'id_forum_comment';

    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'isi',
        'status',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id', 'id_forum_topic');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id_forum_comment');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id', 'id_forum_comment');
    }

    public function publishedReplies()
    {
        return $this->hasMany(self::class, 'parent_id', 'id_forum_comment')
            ->where('status', 'published');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
