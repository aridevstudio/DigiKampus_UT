<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Profile extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'nomor_induk',
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

    public function jurusans()
    {
        return $this->belongsToMany(Jurusan::class, 'dosen_jurusan', 'profile_id', 'jurusan_id')
            ->withTimestamps()
            ->orderBy('nama_jurusan');
    }

    protected function jurusanIds(): Attribute
    {
        return Attribute::get(function (): array {
            if ($this->relationLoaded('jurusans') && $this->jurusans->isNotEmpty()) {
                return $this->jurusans
                    ->pluck('id_jurusan')
                    ->map(fn ($id) => (string) $id)
                    ->values()
                    ->all();
            }

            if (blank($this->id_jurusan)) {
                return [];
            }

            return [(string) $this->id_jurusan];
        });
    }

    protected function jurusanNames(): Attribute
    {
        return Attribute::get(function (): array {
            if ($this->relationLoaded('jurusans') && $this->jurusans->isNotEmpty()) {
                return $this->jurusans
                    ->pluck('nama_jurusan')
                    ->values()
                    ->all();
            }

            if ($this->relationLoaded('jurusan') && $this->jurusan) {
                return [$this->jurusan->nama_jurusan];
            }

            return [];
        });
    }
}
