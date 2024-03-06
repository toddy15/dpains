<?php

namespace App\Providers;

use App\Http\Middleware\TrimStrings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();
        // The whitespace in the fields 'people' and 'shifts'
        // is significant for raw plans, so do not trim strings.
        TrimStrings::skipWhen(fn ($request) => $request->url() === route('rawplans.store'));
    }
}
