<?php

declare(strict_types=1);

namespace Tests\Integration\App\Crypto\Hashing;

use App\Crypto\Hashing\BcryptPasswordHashingMethod;
use App\Model\Primitive\HashedString;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function password_verify;

class BcryptPasswordHashingMethodTest extends TestCase
{
    private BcryptPasswordHashingMethod $hashingMethod;

    protected function setUp(): void
    {
        $this->hashingMethod = new BcryptPasswordHashingMethod();
    }

    #[Test]
    public function itWillHashDataCorrectly(): void
    {
        $hashed = $this->hashingMethod->hash(new HiddenString('abc123'));

        self::assertNotSame('abc123', $hashed->value);
        self::assertStringStartsWith('$2y', $hashed->value);
        self::assertTrue(password_verify('abc123', $hashed->value));
    }

    #[Test]
    public function itWillVerifyDataCorrectly(): void
    {
        $hash = new HashedString('$2y$13$TCs9vCTPHpODIKSTmf8QuugorcBuLtoO/Hh7m7BCcJyj8EisUCTEa'); // abc123

        $result = $this->hashingMethod->verify(new HiddenString('abc123'), $hash);

        self::assertTrue($result);

        $result = $this->hashingMethod->verify(new HiddenString('other'), $hash);

        self::assertFalse($result);
    }
}
