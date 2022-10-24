<?php

namespace rohsyl\Salto;

use Illuminate\Support\ServiceProvider;
use rohsyl\Salto\Commands\SaltoClientCommand;
use rohsyl\Salto\Commands\SaltoServerCommand;

class SaltoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {


    }

    /**
     * Register the application services.
     */
    public function register()
    {

        if(!$this->app->isProduction() && $this->app->runningInConsole()) {
            $this->commands([
                SaltoServerCommand::class,
                SaltoClientCommand::class
            ]);
        }

    }
}
