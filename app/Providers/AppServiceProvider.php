<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// add this code for fixing error migration
// source: https://medium.com/@chrissoemma/laravel-5-8-solving-first-time-migrations-errors-f8203387b796
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // add this code for fixing error migration
        Schema::defaultStringLength(191);
    }
}
