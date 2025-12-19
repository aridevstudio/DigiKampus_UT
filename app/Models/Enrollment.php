<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';
    protected $primaryKey = 'id_enroll';

    protected $fillable = [
        'id_mahasiswa',
        'id_course',
        'tanggal_daftar',
        'progress',
        'status'
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'progress' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the mahasiswa that owns the enrollment.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    /**
     * Get the course that is enrolled.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    /**
     * Scope by course tipe.
     */
    public function scopeByCourseTipe($query, string $tipe)
    {
        return $query->whereHas('course', function ($q) use ($tipe) {
            $q->where('tipe', $tipe);
        });
    }

    /**
     * Scope for active enrollments.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope for completed enrollments.
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Recalculate progress based on completed materials.
     */
    public function recalculateProgress(int $mahasiswaId): void
    {
        $totalMaterials = $this->course->materials()->count();

        if ($totalMaterials === 0) {
            $this->progress = 0;
            $this->save();
            return;
        }

        $completedMaterials = MaterialProgress::whereIn(
            'id_material',
            $this->course->materials()->pluck('id_material')
        )
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('is_completed', true)
            ->count();

        $this->progress = round(($completedMaterials / $totalMaterials) * 100, 0);

        // Mark as completed if 100%
        if ($this->progress >= 100) {
            $this->status = 'selesai';
        }

        $this->save();
    }
}
