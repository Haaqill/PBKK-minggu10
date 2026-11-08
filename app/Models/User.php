<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function projectActivityLogs(): HasMany
    {
        return $this->hasMany(ProjectActivityLog::class);
    }

    public function dashboardRouteName(): string
    {
        if ($this->hasRole(['Admin', 'Staf'])) {
            return 'admin.index';
        }

        return match ($this->role) {
            'Admin', 'Staf', 'dosen', 'asdos' => 'admin.index',
            'mahasiswa' => 'mahasiswa.proposal.index',
            default => 'admin.index',
        };
    }
}
