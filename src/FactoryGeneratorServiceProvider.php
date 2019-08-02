<?php

namespace PhpCircle\FactoryGenerator;

use Illuminate\Support\ServiceProvider;
use PhpCircle\FactoryGenerator\Commands\FactoryGeneratorCommand;

class FactoryGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cmd.factory.generator', static function ($app) {
            return $app[FactoryGeneratorCommand::class];
        });

        $this->commands('cmd.factory.generator');
    }
}
