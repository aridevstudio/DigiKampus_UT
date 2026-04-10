<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseGradeRecord extends Model
{
    use HasFactory;

    protected $table = 'course_grade_records';
    protected $primaryKey = 'id_course_grade_record';

    protected $fillable = [
        'id_course',
        'id_mahasiswa',
        'pretest_score',
        'assignment_score',
        'final_assignment_score',
        'status',
        'note',
        'last_update_at',
        'reviewed_at',
    ];

    protected $casts = [
        'pretest_score' => 'decimal:2',
        'assignment_score' => 'decimal:2',
        'final_assignment_score' => 'decimal:2',
        'last_update_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }
}

