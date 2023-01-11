<?php

declare(strict_types=1);

namespace Tests\Unit\App\Model\Entity;

use App\Model\Entity\Account;
use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ThreadTest extends TestCase
{
    #[Test]
    public function itWillStoreDataCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, true);

        self::assertSame('a', $instance->getId());
        self::assertSame($subForum, $instance->getForum());
        self::assertSame($author, $instance->getAuthor());
        self::assertSame($name, $instance->getName());
        self::assertSame($createdAt, $instance->getCreatedAt());
        self::assertTrue($instance->isPinned());
        self::assertFalse($instance->isLocked());
    }

    #[Test]
    public function itWillPinCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, false);

        self::assertFalse($instance->isPinned());

        $instance->pin();

        self::assertTrue($instance->isPinned());
    }

    #[Test]
    public function itWillUnpinCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, true);

        self::assertTrue($instance->isPinned());

        $instance->unpin();

        self::assertFalse($instance->isPinned());
    }

    #[Test]
    public function itWillLockCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, true);

        self::assertFalse($instance->isLocked());

        $instance->lock();

        self::assertTrue($instance->isLocked());
    }

    #[Test]
    public function itWillUnlockCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, true);

        $class = new ReflectionClass(Thread::class);
        $class->getProperty('locked')->setValue($instance, true);

        self::assertTrue($instance->isLocked());

        $instance->unlock();

        self::assertFalse($instance->isLocked());
    }

    #[Test]
    public function itWillMoveForumsCorrectly(): void
    {
        $subForumA = new SubForum('b', 'c', 'd');
        $subForumB = new SubForum('b2', 'c2', 'd2');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForumA, $author, $name, $createdAt, true);

        self::assertSame($subForumA, $instance->getForum());

        $instance->moveTo($subForumB);

        self::assertSame($subForumB, $instance->getForum());
    }

    #[Test]
    public function itWillChangeNameCorrectly(): void
    {
        $subForum = new SubForum('b', 'c', 'd');
        $author = new Account(
            'e',
            new EncryptedString('f'),
            new HashedString('g'),
            new EncryptedString('h'),
            new HashedString('i'),
            new HashedString('j'),
            [],
        );
        $name = new EncryptedString('k');
        $nameB = new EncryptedString('l');
        $createdAt = new DateTimeImmutable('1990-01-02 03:04:05');

        $instance = new Thread('a', $subForum, $author, $name, $createdAt, true);

        self::assertSame($name, $instance->getName());

        $instance->changeName($nameB);

        self::assertSame($nameB, $instance->getName());
    }
}
