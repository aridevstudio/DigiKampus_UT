<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLearningGoal extends Model
{
    protected $table = 'course_learning_goals';
    protected $primaryKey = 'id_goal';

    protected $fillable = [
        'id_course',
        'judul_goal',
        'deskripsi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Get the course that owns this learning goal.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}
