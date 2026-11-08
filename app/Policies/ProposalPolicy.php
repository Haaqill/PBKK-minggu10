<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos', 'mahasiswa'])
            || in_array(
                $user->role,
                ['Admin', 'Staf', 'dosen', 'asdos', 'mahasiswa'],
                true
            );
    }

    public function view(User $user, Proposal $proposal): bool
    {
        if ($user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos']) || in_array($user->role, ['Admin', 'Staf', 'dosen', 'asdos'], true)) {
            return true;
        }

        return $user->role === 'mahasiswa'
            && $proposal->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'mahasiswa';
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $user->role === 'mahasiswa'
            && $proposal->user_id === $user->id
            && $proposal->status === 'menunggu';
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->role === 'mahasiswa'
            && $proposal->user_id === $user->id
            && $proposal->status === 'menunggu';
    }
}