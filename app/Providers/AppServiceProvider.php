<?php

namespace App\Providers;

use App\Models\Proposal;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProposalPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            try {
                if ($user->hasRole('Admin') || $user->role === 'Admin' || $user->role === 'dosen') {
                    return true;
                }
            } catch (\Throwable $e) {
                if ($user->role === 'Admin' || $user->role === 'dosen') {
                    return true;
                }
            }
        });

        Gate::define('view-dashboard', function ($user) {
            return $user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos']) || in_array($user->role, ['Admin', 'Staf', 'dosen', 'asdos'], true);
        });

        Gate::define('delete-projects', function ($user) {
            return $user->hasRole('Admin') || in_array($user->role, ['Admin', 'dosen'], true);
        });

        Gate::define('akses-halaman-dosen', function ($user) {
            return $user->role === 'dosen' || $user->hasRole('Admin') || $user->role === 'Admin';
        });

        Gate::define('lihat-proposal', function ($user) {
            return in_array(
                $user->role,
                ['Admin', 'Staf', 'dosen', 'asdos'],
                true
            ) || $user->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos']);
        });

        Gate::define('input-nilai', function (User $user) {
            return $user->role === 'dosen' || $user->hasRole('Admin') || $user->role === 'Admin';
        });

        Gate::define('setujui-proposal', function ($user) {
            return $user->role === 'dosen' || $user->hasRole('Admin') || $user->role === 'Admin';
        });

        Gate::policy(Proposal::class, ProposalPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        
        RateLimiter::for('proposal-submit', function (Request $request) {
            $user = $request->user();

            return Limit::perMinute(3)
                ->by($user ? 'user:' . $user->id : 'ip:' . $request->ip());
        });
    }
}
