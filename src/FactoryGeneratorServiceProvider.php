<?php

namespace MaxQuebral\LaravelDoctrineFactory;

use Illuminate\Support\ServiceProvider;
use MaxQuebral\LaravelDoctrineFactory\Commands\FactoryGeneratorCommand;

class FactoryGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cmd.factory.generator', function ($app) {
            return $app[FactoryGeneratorCommand::class];
        });

        $this->commands('cmd.factory.generator');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
