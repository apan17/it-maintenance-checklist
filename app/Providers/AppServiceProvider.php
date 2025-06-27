<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use Illuminate\Support\Str;

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
        Stringable::macro('absoluteTitle', function () {
            $string = (string) Str::of($this->value)->kebab()->replace('_', ' ')->title();
            return new Stringable($string);
        });
    }
}
