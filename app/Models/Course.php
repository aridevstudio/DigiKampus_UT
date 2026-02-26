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
        'status',
        'tipe',
        'kategori',
        'harga',
        // P0 FIX: Removed 'rating' and 'jumlah_ulasan' — these are system-calculated
        // and must only be set via recalculateRating()
        'estimasi_waktu',
        'durasi_satuan',
        'level',
        'sertifikat',
        'akses_publik',
        'diskon'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'rating' => 'decimal:1',
        'jumlah_ulasan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Get enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'id_course', 'id_course');
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
}
