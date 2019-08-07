<?php
declare(strict_types=1);

namespace PhpCircle\FactoryGenerator\Commands;

interface MappingInterface
{
    public const TYPES = [
        'email' => '{$faker->unique(true)->email}',
        'string' => '{$faker->text(100)}',
        'boolean' => '{$faker->boolean}',
        'datetime' => '{$faker->datetime}',
        'integer' => '{$faker->randomNumber(4)}'
    ];
}
