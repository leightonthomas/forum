<?php

declare(strict_types=1);

namespace App\Model\Entity\Thread;

use App\Model\Entity\Account;
use App\Model\Entity\Persistent;
use App\Model\Entity\SubForum;
use App\Model\Primitive\EncryptedString;
use App\Repository\Entity\Thread\DoctrineThreadRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineThreadRepository::class)]
#[ORM\Table]
class Thread implements Persistent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'encrypted_string')]
    private EncryptedString $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $locked;

    #[ORM\Column(type: 'boolean')]
    private bool $pinned;

    #[ORM\ManyToOne(Account::class)]
    private Account $author;

    #[ORM\ManyToOne(SubForum::class, null, 'EXTRA_LAZY')]
    private SubForum $forum;

    public function __construct(
        string $id,
        SubForum $forum,
        Account $author,
        EncryptedString $name,
        DateTimeImmutable $createdAt,
        bool $pinned,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->forum = $forum;
        $this->createdAt = $createdAt;
        $this->pinned = $pinned;
        $this->locked = false;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAuthor(): Account
    {
        return $this->author;
    }

    public function pin(): void
    {
        $this->pinned = true;
    }

    public function unpin(): void
    {
        $this->pinned = false;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function unlock(): void
    {
        $this->locked = false;
    }

    public function moveTo(SubForum $forum): void
    {
        $this->forum = $forum;
    }

    public function changeName(EncryptedString $newName): void
    {
        $this->name = $newName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): EncryptedString
    {
        return $this->name;
    }

    public function isPinned(): bool
    {
        return $this->pinned;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function getForum(): SubForum
    {
        return $this->forum;
    }
}
