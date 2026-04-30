<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'requires_password_reset',
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
            'requires_password_reset' => 'boolean',
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

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'id_mahasiswa', 'id');
    }

    public function courseInstructorNotes()
    {
        return $this->hasMany(CourseInstructorNote::class, 'id_dosen', 'id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'id_mahasiswa', 'id');
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

    public function usesImportedDefaultPassword(?string $plainPassword = null): bool
    {
        if (!$this->requires_password_reset) {
            return false;
        }

        $nomorInduk = $this->profile?->nomor_induk;
        if (blank($nomorInduk)) {
            return false;
        }

        $candidate = $plainPassword ?? $nomorInduk;

        return (string) $candidate === (string) $nomorInduk
            && Hash::check((string) $candidate, (string) $this->password);
    }

    public function markPendingBecauseDefaultPassword(): void
    {
        if ($this->status === 'aktif') {
            $this->forceFill(['status' => 'pending'])->save();
        }
    }

    public function clearImportedDefaultPasswordState(): void
    {
        $updates = ['requires_password_reset' => false];

        if ($this->status === 'pending') {
            $updates['status'] = 'aktif';
        }

        $this->forceFill($updates)->save();
    }
}
