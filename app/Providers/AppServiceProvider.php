<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Cart;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Cart::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');

        // Anti-abus des formulaires : limite propre à chaque formulaire, par appareil connecté ou par adresse IP.
        // (En Guinée, beaucoup de clients mobiles partagent la même adresse IP : la limite reste large.)
        RateLimiter::for('forms', fn (Request $r) => Limit::perMinute(20)->by(($r->user()?->id ?: $r->ip()).'|'.$r->path()));

        View::composer('layouts.shop', function ($view) {
            $view->with('cartCount', app(Cart::class)->count());
            try {
                $view->with('navCategories', Category::orderBy('position')->get()->groupBy('gender'));
            } catch (\Throwable) {
                $view->with('navCategories', collect());
            }
        });
    }
}
