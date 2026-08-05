<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function view(User $user, Course $course): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'dosen') {
            return (int) $course->id_dosen === (int) $user->id;
        }

        return $user->role === 'mahasiswa'
            && in_array((string) $course->status, ['aktif', 'selesai'], true);
    }

    /**
     * Authorize access to course content and private course files.
     * Catalog visibility and content ownership are intentionally separate.
     */
    public function viewContent(User $user, Course $course): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'dosen') {
            return (int) $course->id_dosen === (int) $user->id;
        }

        return $user->role === 'mahasiswa'
            && $course->enrollments()
                ->where('id_mahasiswa', $user->id)
                ->whereIn('status', ['aktif', 'in_progress', 'selesai'])
                ->exists();
    }

    public function manage(User $user, Course $course): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'dosen' && (int) $course->id_dosen === (int) $user->id);
    }
}
