<?php
declare(strict_types=1);

namespace MaxQuebral\LaravelDoctrineFactory\Tests\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @ORM\Column(name="`active`", type="boolean")
     *
     * @var bool
     */
    protected $active = false;

    /**
     * @ORM\Column(name="age", type="integer", length=2, options={"unsigned": true})
     *
     * @var bool
     */
    protected $age;

    /**
     * @ORM\Column(name="email", type="string", unique=true)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="name", type="string", length=10)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     *
     * @var string
     */
    protected $userId;
}
