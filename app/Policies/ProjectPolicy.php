<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos', 'mahasiswa'])
            || in_array($user->role, ['Admin', 'Staf', 'dosen', 'asdos', 'mahasiswa'], true)
            || $user->can('view-projects');
    }

    public function view(User $user, Project $project): bool
    {
        return $this->isAdministratorOrStaff($user) || $project->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    /**
     * Hak Eksklusif: Hanya peran Admin murni yang berhak menghapus data (atau mahasiswa untuk proyek miliknya sendiri)
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('Admin') 
            || $user->can('delete-projects')
            || in_array($user->role, ['Admin', 'dosen'], true)
            || ($user->role === 'mahasiswa' && $project->user_id === $user->id);
    }

    /**
     * Hak Eksklusif: Hanya peran Admin murni yang berhak memulihkan data
     */
    public function restore(User $user, Project $project): bool
    {
        return $this->delete($user, $project);
    }

    /**
     * Hak Eksklusif: Hanya peran Admin murni yang berhak melakukan penghapusan massal
     */
    public function bulkDeleteAny(User $user): bool
    {
        return $this->delete($user, new Project());
    }

    private function isAdministratorOrStaff(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos'])
            || in_array($user->role, ['Admin', 'Staf', 'dosen', 'asdos'], true);
    }
}
