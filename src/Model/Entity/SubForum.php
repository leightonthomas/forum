<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Repository\Entity\DoctrineSubForumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineSubForumRepository::class)]
#[ORM\Table]
final class SubForum implements Persistent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function changeName(string $newName): void
    {
        $this->name = $newName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
