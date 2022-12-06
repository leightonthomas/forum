<?php

declare(strict_types=1);

namespace Tests\Integration\App\Crypto\Encryption\Entity;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Model\Entity\Account;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AccountEncryptorTest extends TestCase
{
    private AccountEncryptor $encryptor;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->encryptor = new AccountEncryptor(
            $this->logger,
            'b8a4c452a7fbea2935b6f8881da4ffa6a97e2d788fee0d5250bd9b847b857c22',
        );
    }

    #[Test]
    public function itWillEncryptDataCorrectly(): void
    {
        $result = $this->encryptor->encrypt('a', new HiddenString('b'));

        self::assertStringStartsWith('nacl:', $result->emailAddress->value);
        self::assertSame(
            '414a5615fed0dd671846a7bb22c4d9f133fb8ad2718c8c345daf2253cc0f74d9',
            $result->emailAddressFullBlindIndex->value,
        );
    }

    #[Test]
    public function itWillDecryptDataCorrectly(): void
    {
        $account = new Account(
            'a',
            'username',
            new EncryptedString('nacl:OurRkQPQohVIhfYkzNQ37YpUwb7olMhj6FN0oJt9QSVI809_5RULv4Y='),
            new HashedString('414a5615fed0dd671846a7bb22c4d9f133fb8ad2718c8c345daf2253cc0f74d9'),
            new HashedString('c'),
            ['d'],
        );

        $this->logger
            ->expects(self::once())
            ->method('notice')
            ->with(self::matchesRegularExpression('/Account \[.+] decrypted/'))
        ;

        $result = $this->encryptor->decrypt($account);

        self::assertSame('b', $result->emailAddress->getString());
    }
}
