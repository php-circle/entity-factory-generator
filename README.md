# Laravel Doctrine Factory Generator
Generate factory from an existing entity based on metadata.

#### Installation
```
composer require php-circle/entity-factory-generator
```
#### Usage
To generate an entity factory run the artisan command:
```
php artisan doctrine:generate:entities:factory "App\Database\Entities\User"
```
#### Factory Result
Actual result is `array()`, but you can to format to `[]`.
```
<?php

$factory->define(App\Database\Entities\Acme::class, static function (Generator $faker): array {
    return [
        'active' => $faker->boolean,
        'age' => $faker->numberBetween(1, 50),
        'text' => $faker->text(100),
        'email' => $faker->unique(true)->email,
        'randomNumber' => $faker->randomNumber(4)
    ];
});
```

#### License
The Laravel Doctrine Factory Generator is free software licensed under the MIT license.