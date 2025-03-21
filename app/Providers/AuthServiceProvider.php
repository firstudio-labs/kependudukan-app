<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        // Define a 'superadmin' ability
        Gate::define('superadmin', function ($user) {
            return $user->role === 'superadmin';
        });

        // Define an 'admin' ability
        Gate::define('admin', function ($user) {
            return $user->role === 'admin' || $user->role === 'superadmin';
        });

        // Define an 'operator' ability
        Gate::define('operator', function ($user) {
            return $user->role === 'operator' || $user->role === 'admin' || $user->role === 'superadmin';
        });
    }
}
