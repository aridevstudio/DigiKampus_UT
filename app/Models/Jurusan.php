<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
