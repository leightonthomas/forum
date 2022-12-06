<?php

declare(strict_types=1);

namespace Tests\Unit\App\Model\Primitive;

use App\Model\Primitive\EncryptedString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EncryptedStringTest extends TestCase
{
    #[Test]
    public function itWillStoreDataWithoutModifyingIt(): void
    {
        $instance = new EncryptedString('abc');

        self::assertSame('abc', $instance->value);
    }
}
