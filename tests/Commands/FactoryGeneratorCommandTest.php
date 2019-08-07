<?php
declare(strict_types=1);

namespace Tests\PhpCircle\FactoryGenerator\Commands;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Filesystem\Filesystem;
use LaravelDoctrine\ORM\Testing\Factory;
use PhpCircle\FactoryGenerator\Commands\FactoryGeneratorCommand;
use RuntimeException;
use Tests\PhpCircle\FactoryGenerator\Database\Entities\Acme;
use Mockery\MockInterface;
use Faker\Factory as FakerFactory;
use Tests\PhpCircle\FactoryGenerator\TestCase;

/**
 * @covers \PhpCircle\FactoryGenerator\Commands\FactoryGeneratorCommand
 */
final class FactoryGeneratorCommandTest extends TestCase
{
    /**
     * Should generate factory successfully (Functional test).
     *
     * @return void
     */
    public function xtestFunctionalTestHandleSuccess(): void
    {
        $result = $this->getConsole()->call(
            'doctrine:generate:entity:factory',
            ['entity' => Acme::class, '--force' => true]
        );

        self::assertFalse((bool)$result);
    }

    /**
     * Should force generate factory successfully.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleSuccessForceGenerate(): void
    {
        $faker = FakerFactory::create();

        /** @var \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry */
        $managerRegistry = $this->mock(ManagerRegistry::class);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = \app('em');

        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->mockFilesystem(__DIR__ . '/../../src/factory-template');

        $this->addCommandInput('getArgument', 'entity', Acme::class);
        $this->addCommandInput('getOption', 'force', true);

        $this->setOutputExpectations();

        $command = new FactoryGeneratorCommand($faker, $filesystem, new Factory($faker, $managerRegistry));

        $this->setConsoleInputOutput($command);

        self::assertEquals(0, $command->handle($entityManager));
    }

    /**
     * Should generate factory successfully (force=false).
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleSuccessGenerate(): void
    {
        $faker = FakerFactory::create();

        /** @var \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry */
        $managerRegistry = $this->mock(ManagerRegistry::class);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = \app('em');

        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->mockFilesystem(__DIR__ . '/../../src/factory-template');

        $this->addCommandInput('getArgument', 'entity', Acme::class);
        $this->addCommandInput('getOption', 'force', false);

        $this->setOutputExpectations();

        $command = new FactoryGeneratorCommand($faker, $filesystem, new Factory($faker, $managerRegistry));

        $this->setConsoleInputOutput($command);

        self::assertEquals(0, $command->handle($entityManager));
    }

    /**
     * Should throw exception if factory already exist and force = false.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleThrowException(): void
    {
        /** @noinspection PhpParamsInspection */
        $this->expectException(RuntimeException::class);

        $entity = Acme::class;

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $this->mock(EntityManagerInterface::class);

        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->mock(Filesystem::class);

        $this->addCommandInput('getArgument', 'entity', $entity);
        $this->addCommandInput('getOption', 'force', false);

        /** @var \LaravelDoctrine\ORM\Testing\Factory $factory */
        $factory = $this->mock(
            Factory::class,
            static function (MockInterface $mock) use ($entity): void {
                $mock->shouldReceive('offsetExists')
                    ->once()->with($entity)->andReturnTrue();
            }
        );

        $command = new FactoryGeneratorCommand(
            FakerFactory::create(),
            $filesystem,
            $factory
        );

        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'input')
            ->setValue($command, $this->consoleInput);

        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'output')
            ->setValue($command, $this->consoleOutput);

        $command->handle($entityManager);
    }

    /**
     * This method is called after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Delete generated factories
        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = new Filesystem();
        // $filesystem->delete(database_path('factories/AcmeFactory.php'));

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * Mock Filesystem.
     *
     * @param string $factoryTemplate
     *
     * @return \Mockery\MockInterface
     */
    private function mockFilesystem(string $factoryTemplate): MockInterface
    {
        return $this->mock(
            Filesystem::class,
            static function (MockInterface $mock) use ($factoryTemplate): void {
                $mock->shouldReceive('get')
                    ->once()->with(\realpath($factoryTemplate))
                    ->andReturn(\file_get_contents(\realpath($factoryTemplate)));
                $mock->shouldReceive('put')
                    ->once()->withAnyArgs()->andReturnNull();
            }
        );
    }

    /**
     * Set the console input and output.
     *
     * @param \PhpCircle\FactoryGenerator\Commands\FactoryGeneratorCommand $command
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    private function setConsoleInputOutput(FactoryGeneratorCommand $command): void
    {
        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'input')
            ->setValue($command, $this->consoleInput);

        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'output')
            ->setValue($command, $this->consoleOutput);
    }

    /**
     * Set output expectations.
     *
     * @return void
     */
    private function setOutputExpectations(): void
    {
        $this->consoleOutput->shouldReceive('title')->once()->withAnyArgs()->andReturnNull();
        $this->consoleOutput->shouldReceive('text')->atLeast()->once()->withAnyArgs()->andReturnNull();
    }
}
