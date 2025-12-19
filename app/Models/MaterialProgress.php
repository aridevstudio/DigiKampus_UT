<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProgress extends Model
{
    use HasFactory;

    protected $table = 'material_progress';
    protected $primaryKey = 'id_progress';

    protected $fillable = [
        'id_mahasiswa',
        'id_material',
        'is_completed',
        'watched_duration',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watched_duration' => 'integer',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the mahasiswa that owns the progress.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    /**
     * Get the material for the progress.
     */
    public function material()
    {
        return $this->belongsTo(CourseMaterial::class, 'id_material', 'id_material');
    }
}
