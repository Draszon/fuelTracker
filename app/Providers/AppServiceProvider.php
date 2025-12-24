<?php

namespace App\Providers;

use App\Models\User;
use Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Gate::define('register-user', function (User $user) {
            return $user->is_admin;
        });

        Inertia::share([
            'flash' => function () {
                return [
                    'message' => session('message'),
                ];
            }
        ]);
    }
}
