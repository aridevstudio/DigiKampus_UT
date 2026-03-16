<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id_submission';

    protected $fillable = [
        'id_course',
        'id_material',
        'id_mahasiswa',
        'file_path',
        'original_file_name',
        'file_size',
        'catatan_mahasiswa',
        'catatan_dosen',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function material()
    {
        return $this->belongsTo(CourseMaterial::class, 'id_material', 'id_material');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }
}
