<?php

namespace Nroyliu\Timsdk;


use Illuminate\Support\ServiceProvider;

class TimsdkProvider extends ServiceProvider
{
    public function boot(){
        $this->publishes([
            __DIR__ . "/../config/tim.php" => config_path('tim.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton(Tim::class, function () {
            return new Tim();
        });
    }
}
