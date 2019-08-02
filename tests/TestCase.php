<?php
declare(strict_types=1);

namespace Tests\PhpCircle\FactoryGenerator;

use Closure;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\Lumen\Application;
use Laravel\Lumen\Console\ConsoleServiceProvider;
use Laravel\Lumen\Console\Kernel;
use Laravel\Lumen\Exceptions\Handler;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use PhpCircle\FactoryGenerator\FactoryGeneratorServiceProvider;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @var \Mockery\MockInterface
     */
    protected $consoleInput;

    /**
     * @var \Mockery\MockInterface
     */
    protected $consoleOutput;

    /**
     * Add expected method call in input.
     *
     * @param string $method
     * @param string $input
     * @param mixed $value
     *
     * @return void
     */
    protected function addCommandInput(string $method, string $input, $value): void
    {
        $this->consoleInput->shouldReceive($method)->once()->with($input)->andReturn($value);
    }

    /**
     * Add expected method call in output.
     *
     * @param string $method
     * @param mixed ...$args
     *
     * @return void
     */
    protected function addCommandOutput(string $method, ...$args): void
    {
        $this->consoleOutput->shouldReceive($method)->once()->withArgs($args)->andReturnNull();
    }

    /**
     * Boot lumen application.
     *
     * @return \Laravel\Lumen\Application
     */
    protected function bootLumen(): Application
    {
        if ($this->app !== null) {
            return $this->app;
        }

        $app = new Application(__DIR__ . '/AppEnv');

        $app->register(ConsoleServiceProvider::class);
        $app->register(DoctrineServiceProvider::class);
        $app->register(FactoryGeneratorServiceProvider::class);

        $app->bind(ExceptionHandler::class, Handler::class);
        $app->boot();

        return $this->app = $app;
    }

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
     * Convert protected/private method to public.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException
     */
    protected function getMethodAsPublic(string $className, string $methodName): ReflectionMethod
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Convert protected/private property to public.
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException
     */
    protected function getPropertyAsPublic(string $className, string $propertyName): ReflectionProperty
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Create mock for given class and set expectations using given closure.
     *
     * @param string $class
     * @param \Closure|null $setExpectations
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery)
     */
    protected function mock(string $class, ?Closure $setExpectations = null): MockInterface
    {
        $mock = \Mockery::mock($class);

        // If no expectations, early return
        if ($setExpectations === null) {
            return $mock;
        }

        // Pass mock to closure to set expectations
        $setExpectations($mock);

        return $mock;
    }

    /**
     * This method is called before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->bootLumen();

        $this->consoleInput = $this->mock(InputInterface::class);
        $this->consoleOutput = $this->mock(OutputInterface::class);
    }
}
