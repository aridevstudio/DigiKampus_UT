<?php

namespace App\Policies;

use App\Models\BootcampLiveClassAttendance;
use App\Models\User;

class BootcampLiveClassAttendancePolicy
{
    public function view(User $user, BootcampLiveClassAttendance $attendance): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'mahasiswa' && (int) $attendance->id_user === (int) $user->id)
            || ($user->role === 'dosen' && $attendance->course()->where('id_dosen', $user->id)->exists());
    }
}
