<?php
declare(strict_types=1);

namespace MaxQuebral\LaravelDoctrineFactory\Tests;

final class FactoryGeneratorCommandTest extends TestCase
{
    /**
     * Should generate factory successfully.
     *
     * @return void
     */
    public function testHandleSuccess(): void
    {
        $result = $this->getConsole()->call(
            'doctrine:generate:test-factories',
            ['entity' => 'MaxQuebral\LaravelDoctrineFactory\Tests\Database\Entities\User']
        );

        self::assertFalse((bool)$result);
    }
}
