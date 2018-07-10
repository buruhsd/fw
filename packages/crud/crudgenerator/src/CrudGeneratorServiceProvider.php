<?php

namespace Aljawad\Crudgenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes/web.php';

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/crudgenerator/'),
        ]);

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'crudgenerator');
        $this->app->make('Aljawad\Crudgenerator\CrudGeneratorController');
    }
}
