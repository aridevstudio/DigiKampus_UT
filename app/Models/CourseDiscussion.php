<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDiscussion extends Model
{
    use HasFactory;

    protected $table = 'course_discussions';
    protected $primaryKey = 'id_course_discussion';

    protected $fillable = [
        'id_course',
        'id_user',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
