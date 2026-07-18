<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $primaryKey = 'id_course';

    protected $fillable = [
        'kode_course',
        'nama_course',
        'deskripsi',
        'persyaratan',
        'id_dosen',
        'id_jurusan',
        'thumbnail',
        'youtube_playlist',
        'online_link',
        'status',
        'approval_status',
        'approval_notes',
        'approved_by',
        'approved_at',
        'tipe',
        'kategori',
        'tanggal_webinar',
        'jam_mulai_webinar',
        'jam_selesai_webinar',
        'kuota_peserta',
        'harga',
        // P0 FIX: Removed 'rating' and 'jumlah_ulasan' — these are system-calculated
        // and must only be set via recalculateRating()
        'estimasi_waktu',
        'durasi_satuan',
        'level',
        'sertifikat',
        'certificate_template_id',
        'akses_publik',
        'diskon',
        // Sesi bootcamp: kolom additive utk struktur event-based.
        'tipe_event',
        'mode_event',
        'lokasi_event',
        'peta_event',
        'kapasitas_maksimal',
        'slot_terisi',
        'checkin_required',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'rating' => 'decimal:1',
        'jumlah_ulasan' => 'integer',
        'tanggal_webinar' => 'date',
        'kuota_peserta' => 'integer',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'kapasitas_maksimal' => 'integer',
        'slot_terisi' => 'integer',
        'checkin_required' => 'boolean',
    ];

    /**
     * Get the dosen (instructor) that owns the course.
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id');
    }

    /**
     * Get the jurusan that owns the course.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Get enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'id_course', 'id_course');
    }

    /** Temporary/confirmed checkout reservations for capacity-only events. */
    public function eventSeatReservations()
    {
        return $this->hasMany(EventSeatReservation::class, 'id_course', 'id_course');
    }

    /**
     * Get materials for the course.
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'id_course', 'id_course');
    }

    /**
     * Get modules for the course.
     */
    public function modules()
    {
        return $this->hasMany(CourseModule::class, 'id_course', 'id_course')->orderBy('urutan');
    }

    /**
     * Get assignments for the course.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'id_course', 'id_course');
    }

    public function instructorNotes()
    {
        return $this->hasMany(CourseInstructorNote::class, 'id_course', 'id_course');
    }

    public function youtubePlaylistVideos()
    {
        return $this->hasMany(YoutubePlaylistVideo::class, 'id_course', 'id_course')->orderBy('urutan');
    }

    public function discussions()
    {
        return $this->hasMany(CourseDiscussion::class, 'id_course', 'id_course');
    }

    /**
     * Get learning goals (Tujuan Pembelajaran) attached to this course.
     *
     * Learning goals are course-level competencies the student is expected
     * to achieve after completing the course/bootcamp/webinar. Admin &
     * Dosen can manage them via the course editor; the student views
     * them on course-detail (preview) and on course-learn (achieved
     * state when enrollment is selesai / progress 100%).
     */
    public function learningGoals()
    {
        return $this->hasMany(CourseLearningGoal::class, 'id_course', 'id_course')->orderBy('urutan');
    }

    /**
     * Scope to filter by tipe (pricing: gratis/berbayar).
     */
    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    /**
     * Scope to filter by kategori (format: webinar/tiket/kursus).
     */
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope to get only active courses.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope to search by name or description.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('nama_course', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }

    /**
     * Get ratings for the course.
     */
    public function ratings()
    {
        return $this->hasMany(CourseRating::class, 'id_course', 'id_course');
    }

    /**
     * Check if mahasiswa has rated this course.
     */
    public function hasRatedBy(int $mahasiswaId): bool
    {
        return $this->ratings()->where('id_mahasiswa', $mahasiswaId)->exists();
    }

    /**
     * Get mahasiswa's rating for this course.
     */
    public function getRatingBy(int $mahasiswaId): ?CourseRating
    {
        return $this->ratings()->where('id_mahasiswa', $mahasiswaId)->first();
    }

    /**
     * Recalculate and update rating from user submissions.
     */
    public function recalculateRating(): void
    {
        $ratings = $this->ratings();
        $this->jumlah_ulasan = $ratings->count();
        $this->rating = $ratings->avg('rating') ?? 0;
        $this->save();
    }

    /**
     * Get the certificate template associated with the course.
     */
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id', 'id');
    }

    // ─────────────────────────────────────────────────────────────────────
    // BOOTCAMP SESSIONS — struktur event-based (bootcamp/webinar/workshop/seminar)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Sesi bootcamp aktif (urutan ascending). Untuk legacy bootcamp yang
     * dibuat sebelum migration bootcamp_sessions, lihat
     * {@link \App\Services\BootcampFlowService::resolveActiveSessions()}
     * untuk virtualisasi 1 sesi default.
     */
    public function sessions()
    {
        return $this->hasMany(BootcampSession::class, 'id_course', 'id_course')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('tanggal_sesi')
            ->orderBy('jam_mulai');
    }

    /**
     * Semua sesi (termasuk non-aktif) untuk admin view.
     */
    public function allSessions()
    {
        return $this->hasMany(BootcampSession::class, 'id_course', 'id_course')
            ->orderBy('urutan');
    }

    /** Accessor typed untuk tipe_event. */
    public function getBootcampTypeAttribute(): \App\Support\Bootcamp\BootcampType
    {
        return \App\Support\Bootcamp\BootcampType::fromNullable($this->tipe_event);
    }

    /** Accessor typed untuk mode_event. */
    public function getAccessModeAttribute(): \App\Support\Bootcamp\AccessMode
    {
        return \App\Support\Bootcamp\AccessMode::fromNullable($this->mode_event);
    }

    public function getSlotsRemainingAttribute(): int
    {
        $cap = (int) ($this->kapasitas_maksimal ?? 0);
        if ($cap === 0) {
            return PHP_INT_MAX;
        }
        return max(0, $cap - (int) ($this->slot_terisi ?? 0));
    }

    /**
     * Daftar nilai tipe_event yang diperlakukan sebagai event (bukan kursus reguler).
     *
     * Dipakai sebagai single source of truth di mana pun sistem perlu membedakan
     * event dari kursus — termasuk {@see Course::scopeBootcampStyle()},
     * {@see \App\Http\Controllers\Mahasiswa\DashboardController::isEventCourse()},
     * dan query builder untuk statistik dashboard.
     */
    public const EVENT_TIPE_VALUES = ['bootcamp', 'webinar', 'workshop', 'seminar'];

    /**
     * Scope bootcamp-style events: kategori=tiket OR tipe_event set.
     * (Legacy pakai kategori; baru pakai tipe_event untuk granular type.)
     */
    public function scopeBootcampStyle($query)
    {
        return $query->where(function ($q) {
            $q->where('kategori', 'tiket')
              ->orWhereIn('tipe_event', self::EVENT_TIPE_VALUES);
        });
    }

    public function scopeOfTipeEvent($query, string $tipe)
    {
        return $query->where('tipe_event', $tipe);
    }
}
