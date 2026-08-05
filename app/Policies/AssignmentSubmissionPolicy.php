<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\User;

class AssignmentSubmissionPolicy
{
    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'mahasiswa') {
            return (int) $submission->id_mahasiswa === (int) $user->id;
        }

        return $user->role === 'dosen'
            && $submission->course()->where('id_dosen', $user->id)->exists();
    }
}
