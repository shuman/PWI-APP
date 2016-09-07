<?php

namespace App\Providers;

//use App\Repositories\UserRepository as UserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Validator;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        Validator::extend('CreditCardNumber', 'App\Http\CustomValidator@validateCreditCard');

        Validator::extend('CreditCardCvc', 'App\Http\CustomValidator@validateCvc');

        Validator::extend('CreditCardDate', 'App\Http\CustomValidator@validateCreditCardExpiration');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        
    }
}
