<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubePlaylistVideo extends Model
{
    use HasFactory;

    protected $table = 'youtube_playlist_videos';

    protected $fillable = [
        'id_course',
        'youtube_id',
        'title',
        'thumbnail_url',
        'urutan',
        'duration_seconds',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    /**
     * Get formatted duration (e.g. "5:30").
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = intdiv($this->duration_seconds, 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
