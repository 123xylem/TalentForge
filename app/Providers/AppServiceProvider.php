<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        Inertia::share([
            'userType' => function () { // Share the user type
                if (Auth::check()) {
                    return Auth::user()->type;
                }

                return null; // Or a default value if the user is not logged in
            },
        ]);
    }
}
