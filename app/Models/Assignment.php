<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignments';
    protected $primaryKey = 'id_assignment';

    protected $fillable = [
        'id_course',
        'judul',
        'deskripsi',
        'deadline'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course that owns the assignment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}
