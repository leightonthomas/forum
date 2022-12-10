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
        $result = $this->encryptor->encrypt('a', new HiddenString('c'), new HiddenString('b'));

        self::assertStringStartsWith('nacl:', $result->username->value);
        self::assertStringStartsWith('nacl:', $result->emailAddress->value);
        self::assertSame(
            '477fbbaca15866b55291184a6d78bece4c8a394c548d62ba1f9b105e14fabb00',
            $result->emailAddressFullBlindIndex->value,
        );
        self::assertSame(
            'b89597c2233ee77cadc7ec5d06cd0a684f8e1cad5990e97511e6fd0d440b37c9',
            $result->usernameBlindIndex->value,
        );
    }

    #[Test]
    public function itWillDecryptDataCorrectly(): void
    {
        $account = new Account(
            'a',
            new EncryptedString('nacl:m9qRhAgob4F1745A24Z131UExsN0NTkeS2cjGpsf8ho1CCc3ACuF1ZI='),
            new HashedString('b89597c2233ee77cadc7ec5d06cd0a684f8e1cad5990e97511e6fd0d440b37c9'),
            new EncryptedString('nacl:OurRkQPQohVIhfYkzNQ37YpUwb7olMhj6FN0oJt9QSVI809_5RULv4Y='),
            new HashedString('477fbbaca15866b55291184a6d78bece4c8a394c548d62ba1f9b105e14fabb00'),
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
