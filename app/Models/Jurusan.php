<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans';
    protected $primaryKey = 'id_jurusan';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'fakultas',
        'jenjang',
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'id_jurusan', 'id_jurusan');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'id_jurusan', 'id_jurusan');
    }

    public function dosenProfiles()
    {
        return $this->belongsToMany(Profile::class, 'dosen_jurusan', 'jurusan_id', 'profile_id')
            ->withTimestamps();
    }
}
