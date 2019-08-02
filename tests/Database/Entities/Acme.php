<?php
declare(strict_types=1);

namespace Tests\PhpCircle\FactoryGenerator\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Acme
{
    /**
     * @ORM\Column(name="`active`", type="boolean")
     *
     * @var bool
     */
    protected $active = false;

    /**
     * @ORM\Column(name="address", type="string")
     *
     * @var string
     */
    protected $address;

    /**
     * @ORM\Column(name="age", type="integer", length=2, options={"unsigned": true})
     *
     * @var bool
     */
    protected $age;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @var \DateTime
     */
    protected $createdAt;

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
     * @ORM\Column(name="random_number", type="integer")
     *
     * @var string
     */
    protected $randomNumber;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     *
     * @var string
     */
    protected $userId;
}
