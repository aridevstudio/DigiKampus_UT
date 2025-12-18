<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Profile extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'nim',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'id_jurusan',
        'ipk',
        'total_sks',
        'status_akademik',
        'foto_profile',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }
}
