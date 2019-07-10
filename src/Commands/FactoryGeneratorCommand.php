<?php

namespace MaxQuebral\LaravelDoctrineFactory\Commands;

use Composer\Composer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as LaravelConfig;
use Illuminate\Filesystem\Filesystem;
use LaravelDoctrine\ORM\Testing\Factory;
use ReflectionClass;
use RuntimeException;

class FactoryGeneratorCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate entity test factory used for testing';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctrine:generate:test-factories';

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * Create a new command instance.
     *
     * @param \Faker\Generator $faker
     */
    public function __construct(Generator $faker)
    {
        $this->faker = $faker;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \LaravelDoctrine\ORM\Testing\Factory $factory
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function handle(
        LaravelConfig $config,
        Filesystem $filesystem,
        Factory $factory,
        EntityManagerInterface $entityManager
    ) {
        // Create a factory based on entity properties.
        // php artisan doctrine:generate:test-factories --entity=App\Database\Entities\User --force
        // 1. Check if factory already exist/created for an entity. (--force?)
        // 2.

        $paths = $config->get('doctrine.managers.default.paths');

        // Get all entities
        $files = $filesystem->allFiles($paths[0]);

        foreach ($files as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $entityClass = 'App\\Database\\Entities\\' . $file->getBasename('.php');

            // Validate if not FORCED to generate...
            // $this->validateFactory($entityClass, $factory);

            // Begin here...
            $this->comment(\sprintf('-> %s', $entityClass));

            // $reflection = new ReflectionClass($entityClass);
            // foreach ($reflection->getProperties() as $property) {
            //     dump($property->getDocComment());
            // }

            $metadata = $entityManager->getClassMetadata($entityClass);
            $fields = $metadata->getFieldNames();

            $data = $this->createDataArray($fields, $metadata);
        }

        dd($data);
    }

    /**
     * Add data to the fields of the entity.
     *
     * @param array $fields
     *
     * @return mixed[]
     */
    private function createDataArray(array $fields, ClassMetadata $classMetadata): array
    {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] =  $this->getPropertyValue($field, $classMetadata->getTypeOfField($field));
        }

        return $data;
    }

    /**
     * Get value using faker.
     *
     * @param string $fieldName
     * @param $type
     *
     * @return mixed
     */
    private function getPropertyValue(string $fieldName, $type)
    {
        if (is_string($type) === true) {
            if ($fieldName === 'email') {
                return $this->faker->email;
            }

            return $this->faker->word;
        }

        return null;
    }

    /**
     * Check if factory already exist/created for an entity.
     *
     * @param string $entityClass
     * @param \LaravelDoctrine\ORM\Testing\Factory $factory
     */
    private function validateFactory(string $entityClass, Factory $factory): void
    {
        if ($factory->offsetExists($entityClass) === true) {
            throw new RuntimeException(\sprintf('%s factory already exist', $entityClass));
        }
    }
}
