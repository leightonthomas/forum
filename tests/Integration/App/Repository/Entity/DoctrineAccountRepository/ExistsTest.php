<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineAccountRepository;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('Integration/App/Repository/Entity/DoctrineAccountRepository/Exists')]
class ExistsTest extends DoctrineAccountRepositoryTestCase
{
    #[Test]
    #[DataProvider('existsProvider')]
    #[Fixture('existing.php')]
    public function itWillCorrectlyDetermineIfAccountAlreadyExists(
        string $id,
        string $username,
        string $rawEmail,
        bool $expected,
    ): void {
        $encryptionResult = self::getContainer()
            ->get(AccountEncryptor::class)
            ->encrypt($id, new HiddenString($username), new HiddenString($rawEmail))
        ;

        $result = $this->repository->exists(
            $id,
            $encryptionResult->usernameBlindIndex,
            $encryptionResult->emailAddressFullBlindIndex,
        );

        self::assertSame($expected, $result);
    }

    public static function existsProvider(): array
    {
        return [
            'no field exists' => ['8c7631e6-be7c-4cd5-a543-f7bea415c068', 'abc', 'def456', false],
            'username exists' => ['8c7631e6-be7c-4cd5-a543-f7bea415c068', 'bob', 'def456', true],
            'email exists' => ['8c7631e6-be7c-4cd5-a543-f7bea415c068', 'abc', 'erica@example.com', true],
            'id exists' => ['9afbee8b-303f-4582-a260-b6552810bd33', 'abc', 'def456', true],
        ];
    }
}
