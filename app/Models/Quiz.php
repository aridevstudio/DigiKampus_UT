<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';
    protected $primaryKey = 'id_quiz';

    protected $fillable = [
        'id_module',
        'id_course',
        'judul',
        'deskripsi',
        'durasi_menit',
        'is_pretest',
        'is_active',
        'passing_score',
        'acak_soal',
        'tampilkan_nilai',
        'urutan',
    ];

    protected $casts = [
        'is_pretest' => 'boolean',
        'is_active' => 'boolean',
        'acak_soal' => 'boolean',
        'tampilkan_nilai' => 'boolean',
        'durasi_menit' => 'integer',
        'passing_score' => 'integer',
        'urutan' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'id_module', 'id_module');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'id_quiz', 'id_quiz')->orderBy('urutan');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'id_quiz', 'id_quiz');
    }

    public function getTotalBobotAttribute(): int
    {
        return $this->questions()->sum('bobot');
    }
}
