<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $table = 'course_materials';
    protected $primaryKey = 'id_material';

    protected $fillable = [
        'id_course',
        'judul_material',
        'tipe',
        'konten',
        'video_url',
        'urutan',
        'durasi'
    ];

    protected $casts = [
        'urutan' => 'integer',
        'durasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course that owns the material.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    /**
     * Get progress records for this material.
     */
    public function progress()
    {
        return $this->hasMany(MaterialProgress::class, 'id_material', 'id_material');
    }

    /**
     * Check if material is completed by mahasiswa.
     */
    public function isCompletedBy(int $mahasiswaId): bool
    {
        return $this->progress()
            ->where('id_mahasiswa', $mahasiswaId)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * Get progress for specific mahasiswa.
     */
    public function getProgressFor(int $mahasiswaId): ?MaterialProgress
    {
        return $this->progress()->where('id_mahasiswa', $mahasiswaId)->first();
    }
}
