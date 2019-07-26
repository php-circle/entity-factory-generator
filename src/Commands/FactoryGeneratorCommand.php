<?php
declare(strict_types=1);

namespace MaxQuebral\LaravelDoctrineFactory\Commands;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use LaravelDoctrine\ORM\Testing\Factory;
use RuntimeException;

class FactoryGeneratorCommand extends Command
{
    /**
     * Change this to true to check if factory is already defined.
     *
     * @var bool
     */
    public const VALIDATE_FACTORY = false;

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
    protected $signature = 'doctrine:generate:test-factories {entity}';

    /**
     * @var \LaravelDoctrine\ORM\Testing\Factory
     */
    private $factory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Create a new command instance.
     *
     * @param \Faker\Generator $faker
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \LaravelDoctrine\ORM\Testing\Factory $factory
     */
    public function __construct(Generator $faker, Filesystem $filesystem, Factory $factory)
    {
        $this->faker = $faker;
        $this->filesystem = $filesystem;
        $this->factory = $factory;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle(EntityManagerInterface $entityManager)
    {
        $entity = $this->argument('entity');

        $this->validateFactory($entity);

        $this->output->title(\sprintf('-> %s', $entity));

        $metadata = $entityManager->getClassMetadata($entity);

        $data = [];
        foreach ($metadata->fieldMappings as $fieldMapping) {
            if ($fieldMapping['id'] ?? false === true) {
                continue;
            }

            $data[$fieldMapping['fieldName']] = $this->createFaker($fieldMapping);
        }

        $this->createFactoryFile($metadata, $data);

        return 0;
    }

    /**
     * Create the factory file.
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadata $metadata
     * @param array $data
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function createFactoryFile(ClassMetadata $metadata, array $data): void
    {
        $path = __DIR__ . '/../factory-template';

        $template = $this->filesystem->get(\realpath($path));

        $template = \str_replace(
            ['{entity}', '{data}', '\'{', '}\''],
            [\sprintf('%s::class', $metadata->getName()), var_export($data, true), '', ''],
            $template
        );

        $filename = \sprintf('%sFactory.php', $metadata->getReflectionClass()->getShortName());

        $newFactory = \database_path('factories/') . $filename;
        $this->filesystem->put($newFactory, $template);
    }

    /**
     * Create faker method.
     *
     * @param mixed[] $field
     *
     * @return string
     *
     * @throws \Exception
     */
    private function createFaker(array $field): string
    {
        $length = $field['length'] ?? 0;

        // String
        if ($field['type'] === 'string') {
            if ($field['fieldName'] === 'email') {
                return '{$faker->unique(true)->email}';
            }

            if ($length > 0) {
                return \sprintf('{$faker->text(%d)}', $this->faker->numberBetween(5, $length));
            }

            return '{$faker->text(100)}';
        }

        // Int
        if ($field['type'] === 'integer') {
            // Default number of digits.
            $len = 4;

            if ($length > 0) {
                return \sprintf('{$faker->numberBetween(1, %d)}', $this->faker->randomNumber($length));
            }

            return \sprintf('{$faker->randomNumber(%d)}', $len);
        }

        // Boolean
        if ($field['type'] === 'boolean') {
            return '{$faker->boolean}';
        }

        return '{null}';
    }

    /**
     * Check if factory already exist/created for an entity.
     *
     * @param string $entityClass
     */
    private function validateFactory(string $entityClass): void
    {
        if (self::VALIDATE_FACTORY === true && $this->factory->offsetExists($entityClass) === true) {
            throw new RuntimeException(\sprintf('%sFactory already exist', $entityClass));
        }
    }
}
