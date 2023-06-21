<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Add post policy
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate for admins to access certain services only
        Gate::define('adminsViewOnly', function ($user) {
            return $user->isAdmin === 1;
        });
    }
}
