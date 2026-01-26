<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\User;
use App\Policies\DatasetPolicy;
use App\Policies\DataPointPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Dataset::class => DatasetPolicy::class,
        DataPoint::class => DataPointPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin gate
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // Statistician gate
        Gate::define('statistician', function (User $user) {
            return $user->role === 'statistician';
        });

        // Provider gate
        Gate::define('provider', function (User $user) {
            return $user->role === 'provider';
        });
    }
}
