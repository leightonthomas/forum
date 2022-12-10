<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineAccountRepository;

use App\Model\Entity\Account;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('Integration/App/Repository/Entity/DoctrineAccountRepository/FindById')]
class FindByIdTest extends DoctrineAccountRepositoryTestCase
{
    #[Test]
    #[Fixture('existing.php')]
    public function itWillCorrectlyRetrieveAnAccountIfOneExistsWithId(): void
    {
        $id = '9afbee8b-303f-4582-a260-b6552810bd33';
        $result = $this->repository->findById($id);

        self::assertInstanceOf(Account::class, $result);
        self::assertSame($id, $result->getId());
        self::assertSame(['ROLE_USER'], $result->getClaims());
    }

    #[Test]
    #[Fixture('existing.php')]
    public function itWillReturnNullIfNoAccountWithId(): void
    {
        $id = '22da1e51-39ea-4ef0-a4c6-33c699816d27';
        $result = $this->repository->findById($id);

        self::assertNull($result);
    }
}
