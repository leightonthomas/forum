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

        $instance = new Account('f', 'g', $emailAddress, $emailAddressBlindIndex, $password, $claims);

        self::assertSame($emailAddress, $instance->getEmailAddress());
        self::assertSame('f', $instance->getId());
        self::assertSame('g', $instance->getUsername());
        self::assertSame($password, $instance->getPassword());
        self::assertSame($claims, $instance->getClaims());

        $class = new ReflectionClass(Account::class);
        self::assertSame($emailAddressBlindIndex, $class->getProperty('emailAddressBlindIndex')->getValue($instance));
    }

    #[Test]
    public function itWillChangeUsernameCorrectly(): void
    {
        $instance = new Account('a', 'b', new EncryptedString('c'), new HashedString('d'), new HashedString('e'), []);

        self::assertSame('b', $instance->getUsername());

        $instance->changeUsername('hello');

        self::assertSame('hello', $instance->getUsername());
    }

    #[Test]
    public function itWillChangePasswordCorrectly(): void
    {
        $instance = new Account('a', 'b', new EncryptedString('c'), new HashedString('d'), new HashedString('e'), []);

        self::assertSame('e', $instance->getPassword()->value);

        $instance->changePassword(new HashedString('hello'));

        self::assertSame('hello', $instance->getPassword()->value);
    }

    #[Test]
    public function itWillChangeEmailAddressCorrectly(): void
    {
        $instance = new Account('a', 'b', new EncryptedString('c'), new HashedString('d'), new HashedString('e'), []);

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
