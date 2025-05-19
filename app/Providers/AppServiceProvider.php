<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

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
        // Define custom redirect logic based on user role
        RedirectIfAuthenticated::redirectUsing(function () {
            if (Auth::check()) {
                if (Auth::user()->isAdmin()) {
                    return route('admin.dashboard');
                } elseif (Auth::user()->role === 'employee') {
                    return route('employee.dashboard');
                }
            }
            
            return '/login'; // Fallback, shouldn't happen if role is set correctly
        });

        // Register URL generator singleton
        $this->app->singleton(
            \Illuminate\Contracts\Routing\UrlGenerator::class,
            function ($app) {
                return new \Illuminate\Routing\UrlGenerator(
                    $app['router']->getRoutes(), 
                    $app['request'], 
                    $app['config']['app.asset_url']
                );
            }
        );

        // Rate limiter for API routes
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}