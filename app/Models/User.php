<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_online',
        'last_activity'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_activity' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'id_mahasiswa', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'id_mahasiswa', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_mahasiswa', 'id');
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'id_mahasiswa', 'id');
    }

    /**
     * Update online status.
     */
    public function setOnline(): void
    {
        $this->is_online = true;
        $this->last_activity = Carbon::now();
        $this->save();
    }

    /**
     * Update offline status.
     */
    public function setOffline(): void
    {
        $this->is_online = false;
        $this->save();
    }
}
