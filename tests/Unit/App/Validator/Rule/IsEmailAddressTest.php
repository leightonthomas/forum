<?php

declare(strict_types=1);

namespace Tests\Unit\App\Validator\Rule;

use App\Validator\Rule\IsEmailAddress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsEmailAddressTest extends TestCase
{
    #[Test]
    public function itWillStoreDefaultMessagesOnConstruction(): void
    {
        $instance = new IsEmailAddress();

        self::assertSame(
            [
                IsEmailAddress::ERR_INVALID => 'This value must be a valid email address.',
            ],
            $instance->getMessages(),
        );
    }

    #[Test]
    public function itWillAllowMessagesToBeOverridden(): void
    {
        $instance = new IsEmailAddress();
        $instance->setMessage(IsEmailAddress::ERR_INVALID, 'my new msg');

        self::assertSame(
            [
                IsEmailAddress::ERR_INVALID => "my new msg",
            ],
            $instance->getMessages(),
        );
    }
}
