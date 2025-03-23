<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define role-based gates
        Gate::define('is-superadmin', function (User $user) {
            return $user->role === 'superadmin';
        });

        Gate::define('is-admin-desa', function (User $user) {
            return $user->role === 'admin desa';
        });

        Gate::define('is-admin-kabupaten', function (User $user) {
            return $user->role === 'admin kabupaten';
        });

        Gate::define('is-operator', function (User $user) {
            return $user->role === 'operator';
        });

        // Gate for users who can manage users (superadmin + admin kabupaten)
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role, ['superadmin', 'admin kabupaten']);
        });

        // Gate for users who can view village-level data
        Gate::define('view-village-data', function (User $user) {
            return in_array($user->role, ['superadmin', 'admin kabupaten', 'admin desa']);
        });

        // Gate for users who can manage village-level data
        Gate::define('manage-village-data', function (User $user) {
            return in_array($user->role, ['superadmin', 'admin desa']);
        });
    }
}
