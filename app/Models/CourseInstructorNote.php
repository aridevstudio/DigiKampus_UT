<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInstructorNote extends Model
{
    use HasFactory;

    protected $table = 'course_instructor_notes';
    protected $primaryKey = 'id_course_instructor_note';

    protected $fillable = [
        'id_course',
        'id_dosen',
        'judul',
        'konten',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id');
    }
}
