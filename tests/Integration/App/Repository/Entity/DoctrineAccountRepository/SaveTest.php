<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineAccountRepository;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Model\Entity\Account;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('Integration')]
class SaveTest extends DoctrineAccountRepositoryTestCase
{
    #[Test]
    #[Fixture('empty.php')]
    public function itWillCorrectlySaveAnAccount(): void
    {
        $id = 'dc4a5bd6-fe6e-47c7-914a-d5817de7b58e';

        $hasher = self::getContainer()->get('crypto.hashing.entity.account.password_hashing_method');

        $encryptionResult = self::getContainer()
            ->get(AccountEncryptor::class)
            ->encrypt($id, new HiddenString('bob'), new HiddenString('test@example.com'))
        ;

        $account = new Account(
            $id,
            $encryptionResult->username,
            $encryptionResult->usernameBlindIndex,
            $encryptionResult->emailAddress,
            $encryptionResult->emailAddressFullBlindIndex,
            $hasher->hash(new HiddenString('apassword')),
            ['ROLE_ADMIN'],
        );

        self::assertSame(0, $this->repository->count([]));

        $this->repository->save($account);

        $results = $this->repository->findAll();
        self::assertCount(1, $results);

        $result = $results[0] ?? null;
        self::assertInstanceOf(Account::class, $result);

        self::assertSame($id, $result->getId());
        self::assertSame($encryptionResult->username->value, $result->getUsername()->value);
        self::assertSame($encryptionResult->emailAddress->value, $result->getEmailAddress()->value);
        self::assertSame(['ROLE_ADMIN'], $result->getClaims());
        self::assertTrue($hasher->verify(new HiddenString('apassword'), $result->getPassword()));
    }
}
