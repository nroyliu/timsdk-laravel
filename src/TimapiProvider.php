<?php

namespace Nroyliu\Timapi;


use Illuminate\Support\ServiceProvider;

class TimapiProvider extends ServiceProvider
{
    public function boot(){
        $this->publishes([
            __DIR__ . "/../config/timapi.php" => config_path('timapi.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton(Timapi::class, function () {
            return new Timapi();
        });
    }
}
