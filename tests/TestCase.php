<?php
declare(strict_types=1);

namespace MaxQuebral\LaravelDoctrineFactory\Tests;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\Lumen\Application;
use Laravel\Lumen\Console\ConsoleServiceProvider;
use Laravel\Lumen\Console\Kernel;
use Laravel\Lumen\Exceptions\Handler;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use MaxQuebral\LaravelDoctrineFactory\FactoryGeneratorServiceProvider;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var \Laravel\Lumen\Application
     */
    protected $app;

    /**
     * @var \Laravel\Lumen\Console\Kernel
     */
    protected $console;

    /**
     * Get console kernel instance.
     *
     * @return \Illuminate\Contracts\Console\Kernel
     */
    protected function getConsole(): KernelContract
    {
        if ($this->console !== null) {
            return $this->console;
        }

        return $this->console = $this->app->make(Kernel::class);
    }

    /**
     * @return \Laravel\Lumen\Application
     */
    protected function getLumenApplication(): Application
    {
        if ($this->app !== null) {
            return $this->app;
        }

        $app = new Application(__DIR__);

        $app->register(ConsoleServiceProvider::class);
        $app->register(DoctrineServiceProvider::class);
        $app->register(FactoryGeneratorServiceProvider::class);

        $app->bind(ExceptionHandler::class, Handler::class);

        $app->boot();

        return $this->app = $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getLumenApplication();
    }
}
