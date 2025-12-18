<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $primaryKey = 'id_course';

    protected $fillable = [
        'kode_course',
        'nama_course',
        'deskripsi',
        'id_dosen',
        'id_jurusan',
        'thumbnail',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the dosen (instructor) that owns the course.
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id');
    }

    /**
     * Get the jurusan that owns the course.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    /**
     * Get enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'id_course', 'id_course');
    }

    /**
     * Get materials for the course.
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'id_course', 'id_course');
    }

    /**
     * Get assignments for the course.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'id_course', 'id_course');
    }
}
