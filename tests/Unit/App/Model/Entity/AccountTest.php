<?php

declare(strict_types=1);

namespace Tests\Unit\App\Model\Entity;

use App\Model\Entity\Account;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AccountTest extends TestCase
{
    #[Test]
    public function itWillStoreDataCorrectly(): void
    {
        $emailAddress = new EncryptedString('a');
        $emailAddressBlindIndex = new HashedString('b');
        $password = new HashedString('c');
        $claims = ['d', 'e'];
        $username = new EncryptedString('f');
        $usernameBlindIndex = new HashedString('g');

        $instance = new Account(
            'f',
            $username,
            $usernameBlindIndex,
            $emailAddress,
            $emailAddressBlindIndex,
            $password,
            $claims,
        );

        self::assertSame($emailAddress, $instance->getEmailAddress());
        self::assertSame('f', $instance->getId());
        self::assertSame($username, $instance->getUsername());
        self::assertSame($password, $instance->getPassword());
        self::assertSame($claims, $instance->getClaims());

        $class = new ReflectionClass(Account::class);
        self::assertSame($emailAddressBlindIndex, $class->getProperty('emailAddressBlindIndex')->getValue($instance));
        self::assertSame($usernameBlindIndex, $class->getProperty('usernameBlindIndex')->getValue($instance));
    }

    #[Test]
    public function itWillChangeUsernameCorrectly(): void
    {
        $oldUsername = new EncryptedString('b');
        $oldUsernameBlindIndex = new HashedString('f');

        $instance = new Account(
            'a',
            $oldUsername,
            $oldUsernameBlindIndex,
            new EncryptedString('c'),
            new HashedString('d'),
            new HashedString('e'),
            [],
        );

        $class = new ReflectionClass(Account::class);

        self::assertSame($oldUsername, $instance->getUsername());
        self::assertSame($oldUsernameBlindIndex, $class->getProperty('usernameBlindIndex')->getValue($instance));

        $newUsername = new EncryptedString('newA');
        $newUsernameBlindIndex = new HashedString('newB');

        $instance->changeUsername($newUsername, $newUsernameBlindIndex);

        self::assertSame($newUsername, $instance->getUsername());
        self::assertSame($newUsernameBlindIndex, $class->getProperty('usernameBlindIndex')->getValue($instance));
    }

    #[Test]
    public function itWillChangePasswordCorrectly(): void
    {
        $instance = new Account(
            'a',
            new EncryptedString('b'),
            new HashedString('f'),
            new EncryptedString('c'),
            new HashedString('d'),
            new HashedString('e'),
            [],
        );

        self::assertSame('e', $instance->getPassword()->value);

        $instance->changePassword(new HashedString('hello'));

        self::assertSame('hello', $instance->getPassword()->value);
    }

    #[Test]
    public function itWillChangeEmailAddressCorrectly(): void
    {
        $instance = new Account(
            'a',
            new EncryptedString('b'),
            new HashedString('f'),
            new EncryptedString('c'),
            new HashedString('d'),
            new HashedString('e'),
            [],
        );

        self::assertSame('c', $instance->getEmailAddress()->value);

        $instance->changeEmailAddress(new EncryptedString('new 1'), new HashedString('new 2'));

        self::assertSame('new 1', $instance->getEmailAddress()->value);

        $class = new ReflectionClass(Account::class);
        self::assertSame(
            'new 2',
            $class->getProperty('emailAddressBlindIndex')->getValue($instance)->value,
        );
    }
}
