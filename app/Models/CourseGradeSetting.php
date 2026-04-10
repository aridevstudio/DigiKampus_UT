<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseGradeSetting extends Model
{
    use HasFactory;

    protected $table = 'course_grade_settings';
    protected $primaryKey = 'id_course_grade_setting';

    protected $fillable = [
        'id_course',
        'pretest_weight',
        'assignment_weight',
        'final_weight',
        'has_final_assignment',
        'is_published',
        'draft_saved_at',
        'published_at',
    ];

    protected $casts = [
        'pretest_weight' => 'integer',
        'assignment_weight' => 'integer',
        'final_weight' => 'integer',
        'has_final_assignment' => 'boolean',
        'is_published' => 'boolean',
        'draft_saved_at' => 'datetime',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}

