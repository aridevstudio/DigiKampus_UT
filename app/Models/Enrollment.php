<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';
    protected $primaryKey = 'id_enroll';

    protected $fillable = [
        'id_mahasiswa',
        'id_course',
        'tanggal_daftar',
        'progress',
        'status'
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'progress' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the mahasiswa that owns the enrollment.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    /**
     * Get the course that is enrolled.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}
