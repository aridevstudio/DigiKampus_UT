<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';
    protected $primaryKey = 'id_attempt';

    protected $fillable = [
        'id_quiz',
        'id_mahasiswa',
        'skor',
        'total_poin',
        'persentase',
        'status',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected $casts = [
        'skor' => 'integer',
        'total_poin' => 'integer',
        'persentase' => 'decimal:2',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz', 'id_quiz');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'id_attempt', 'id_attempt');
    }
}
