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
        // contoh: \App\Models\Course::class => \App\Policies\CoursePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate admin
        Gate::define('admin', function ($user) {
            return method_exists($user, 'isAdmin') && $user->isAdmin();
        });

        // Gate mentor
        Gate::define('mentor', function ($user) {
            return method_exists($user, 'isMentor') && $user->isMentor();
        });

        // Gate gabungan: admin ATAU mentor (untuk dashboard/backoffice)
        Gate::define('backoffice', function ($user) {
            return ($user->isAdmin() ?? false) || ($user->isMentor() ?? false);
        });
    }
}
