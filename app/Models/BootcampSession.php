<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Sesi individual untuk event bootcamp-style (multi/single session).
 *
 * Status cycle yang dipancarkan via {@link status()} accessor:
 *   - upcoming : sesi belum dimulai
 *   - live     : sesi dalam window waktu mulai..selesai
 *   - ended    : sesi sudah lewat
 *   - inactive : admin toggle is_active=false
 *
 * session_key() dipakai sebagai namespace di
 * {@link \App\Models\BootcampLiveClassAttendance::$session_key}
 * sehingga attendance row lama (primary/qa_*) tetap koheren dan row baru
 * memakai 'sesi_<id>'.
 */
class BootcampSession extends Model
{
    use HasFactory;

    protected $table = 'bootcamp_sessions';
    protected $primaryKey = 'id_bootcamp_session';

    protected $fillable = [
        'id_course',
        'judul_sesi',
        'tanggal_sesi',
        'jam_mulai',
        'jam_selesai',
        'link_zoom',
        'link_meet',
        'link_rekaman',
        'materi_file',
        'materi_url',
        'deskripsi_sesi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_sesi' => 'date',
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    /**
     * Carbon: kapan sesi ini dimulai (gabungan tanggal + jam_mulai).
     */
    public function getStartAtAttribute(): Carbon
    {
        return Carbon::parse(
            $this->tanggal_sesi->format('Y-m-d') . ' ' . ($this->jam_mulai ?: '08:00:00')
        );
    }

    /**
     * Carbon: kapan sesi ini berakhir. Default end-of-day bila admin tidak
     * mengisi jam_selesai.
     */
    public function getEndAtAttribute(): Carbon
    {
        $time = $this->jam_selesai ?: '23:59:59';
        return Carbon::parse($this->tanggal_sesi->format('Y-m-d') . ' ' . $time);
    }

    /**
     * string identifier untuk {@link \App\Models\BootcampLiveClassAttendance}.
     * Format: 'sesi_<id>'. Konsisten untuk semua row baru; baris lama
     * ('primary', 'qa_*') tetap valid.
     */
    public function sessionKey(): string
    {
        return 'sesi_' . (int) $this->id_bootcamp_session;
    }

    /**
     * Lifecycle state sekarang, untuk UI badge.
     */
    public function status(): string
    {
        if (!(bool) $this->is_active) {
            return 'inactive';
        }
        $now = Carbon::now();
        if ($now->lessThan($this->start_at)) {
            return 'upcoming';
        }
        if ($now->lessThanOrEqualTo($this->end_at)) {
            return 'live';
        }
        return 'ended';
    }

    public function isUpcoming(): bool { return $this->status() === 'upcoming'; }
    public function isLive(): bool     { return $this->status() === 'live'; }
    public function isEnded(): bool    { return $this->status() === 'ended'; }

    /**
     * Sumber URL canonical untuk tombol JOIN.
     * Preferensi: Zoom dulu, fallback ke Meet.
     */
    public function joinUrl(): ?string
    {
        return $this->link_zoom ?: $this->link_meet;
    }

    public function materiUrl(): ?string
    {
        // Prefer storage-url when materi_file looks like a stored path;
        // fallback ke raw URL.
        if ($this->materi_url) {
            return $this->materi_url;
        }
        if ($this->materi_file) {
            $path = ltrim($this->materi_file, '/');
            return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
                ? \Illuminate\Support\Facades\Storage::url($path)
                : null;
        }
        return null;
    }
}
