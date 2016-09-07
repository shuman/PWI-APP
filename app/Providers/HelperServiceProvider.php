<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Helper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Helper', function( ){
            return new Helper;
        });
    }
}
