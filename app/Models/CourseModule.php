<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseModule extends Model
{
    use HasFactory;

    protected $table = 'course_modules';
    protected $primaryKey = 'id_module';
    protected $fillable = ['id_course', 'judul_module', 'deskripsi', 'urutan'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'id_module')->orderBy('urutan');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'id_module', 'id_module')->orderBy('urutan');
    }

    public function pretest()
    {
        return $this->hasOne(Quiz::class, 'id_module', 'id_module')->where('is_pretest', true);
    }

    public function getVideoCountAttribute(): int
    {
        return $this->materials()->where('tipe', 'video')->count();
    }
}
