<?php
declare(strict_types=1);

namespace MaxQuebral\LaravelDoctrineFactory\Tests;

use Doctrine\Common\Persistence\ManagerRegistry;
use Illuminate\Filesystem\Filesystem;
use LaravelDoctrine\ORM\Testing\Factory;
use MaxQuebral\LaravelDoctrineFactory\Commands\FactoryGeneratorCommand;
use MaxQuebral\LaravelDoctrineFactory\Tests\Database\Entities\Acme;
use Mockery\MockInterface;
use Faker\Factory as FakerFactory;

/**
 * Class FactoryGeneratorCommandTest
 *
 * @covers \MaxQuebral\LaravelDoctrineFactory\Commands\FactoryGeneratorCommand
 *
 * @package MaxQuebral\LaravelDoctrineFactory\Tests
 */
final class FactoryGeneratorCommandTest extends TestCase
{
    /**
     * Should generate factory successfully (Functional test).
     *
     * @return void
     */
    public function testFunctionalTestHandleSuccess(): void
    {
        $result = $this->getConsole()->call(
            'doctrine:generate:test-factories',
            ['entity' => Acme::class]
        );

        self::assertFalse((bool)$result);
    }

    /**
     * Should generate factory successfully.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testHandleSuccess(): void
    {
        $entity = Acme::class;

        $faker = FakerFactory::create();

        /** @var \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry */
        $managerRegistry = $this->mock(ManagerRegistry::class);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = \app('em');

        $factoryTemplate = __DIR__ . '/../src/factory-template';

        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->mock(
            Filesystem::class,
            static function (MockInterface $mock) use ($factoryTemplate): void {

                $mock->shouldReceive('get')
                    ->once()->with(\realpath($factoryTemplate))
                    ->andReturn(\file_get_contents(\realpath($factoryTemplate)));
                $mock->shouldReceive('put')
                    ->once()->withAnyArgs()->andReturnNull();
            }
        );


        $this->addCommandInput(
            'getArgument',
            'entity',
            $entity
        );

        // $this->addCommandOutput('title', \sprintf('-> %s', $entity));
        $this->consoleOutput->shouldReceive('title')->once()->withAnyArgs()->andReturnNull();
        $this->consoleOutput->shouldReceive('text')->once()->withAnyArgs()->andReturnNull();

        $command = new FactoryGeneratorCommand(
            $faker,
            $filesystem,
            new Factory($faker, $managerRegistry)
        );

        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'input')
            ->setValue($command, $this->consoleInput);

        $this->getPropertyAsPublic(FactoryGeneratorCommand::class, 'output')
            ->setValue($command, $this->consoleOutput);

        self::assertEquals(0, $command->handle($entityManager));
    }
}
