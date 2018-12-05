<?php

namespace App\Providers;

use App\Exceptions\ApiHandler;
use App\Http\Controllers\Responses\ApiResponder;
use App\Http\Controllers\Responses\Responder;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(ExceptionHandler::class,ApiHandler::class);
        $this->app->singleton(Responder::class,ApiResponder::class);
    }
}
