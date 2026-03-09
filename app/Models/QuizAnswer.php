<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'quiz_answers';
    protected $primaryKey = 'id_answer';

    protected $fillable = [
        'id_attempt',
        'id_question',
        'jawaban',
        'is_correct',
        'poin_diperoleh',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'poin_diperoleh' => 'integer',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'id_attempt', 'id_attempt');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'id_question', 'id_question');
    }
}
