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
        $instance = new SubForum('a', 'b');

        self::assertSame('a', $instance->getId());
        self::assertSame('b', $instance->getName());
    }

    #[Test]
    public function itWillChangeTheNameCorrectly(): void
    {
        $instance = new SubForum('a', 'b');

        self::assertSame('b', $instance->getName());

        $instance->changeName('c');

        self::assertSame('c', $instance->getName());
    }
}
