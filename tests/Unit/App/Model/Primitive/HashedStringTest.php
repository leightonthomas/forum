<?php

declare(strict_types=1);

namespace Tests\Unit\App\Model\Primitive;

use App\Model\Primitive\HashedString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HashedStringTest extends TestCase
{
    #[Test]
    public function itWillStoreDataWithoutModifyingIt(): void
    {
        $instance = new HashedString('abc');

        self::assertSame('abc', $instance->value);
    }
}
