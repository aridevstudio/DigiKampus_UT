<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';
    protected $primaryKey = 'id_question';

    protected $fillable = [
        'id_quiz',
        'pertanyaan',
        'tipe',
        'opsi',
        'jawaban_benar',
        'bobot',
        'penjelasan',
        'urutan',
    ];

    protected $casts = [
        'opsi' => 'array',
        'bobot' => 'integer',
        'urutan' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz', 'id_quiz');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'id_question', 'id_question');
    }
}
