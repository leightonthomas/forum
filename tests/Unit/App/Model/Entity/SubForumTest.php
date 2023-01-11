<?php

declare(strict_types=1);

namespace Tests\Unit\App\Model\Entity;

use App\Model\Entity\SubForum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SubForumTest extends TestCase
{
    #[Test]
    public function itWillStoreDataCorrectly(): void
    {
        $instance = new SubForum('a', 'b', 'c');

        self::assertSame('a', $instance->getId());
        self::assertSame('b', $instance->getName());
        self::assertSame('c', $instance->getSlug());
    }

    #[Test]
    public function itWillChangeTheNameCorrectly(): void
    {
        $instance = new SubForum('a', 'b', 'e');

        self::assertSame('b', $instance->getName());
        self::assertSame('e', $instance->getSlug());

        $instance->changeName('c', 'd');

        self::assertSame('c', $instance->getName());
        self::assertSame('d', $instance->getSlug());
    }
}
