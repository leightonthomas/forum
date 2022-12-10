<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineSubForumRepository;

use App\Model\Entity\SubForum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('Integration')]
class ListTest extends DoctrineSubForumRepositoryTestCase
{
    #[Test]
    #[Fixture('empty.php')]
    public function itWillReturnEmptyIfNoResults(): void
    {
        $results = $this->repository->list();

        self::assertEmpty($results);
    }

    #[Test]
    #[Fixture('App/Repository/Entity/DoctrineSubForumRepository/List/existing.php')]
    public function itWillReturnAllSubForumsOrderedAlphabetically(): void
    {
        $results = $this->repository->list();

        self::assertCount(5, $results);

        $results = array_map(fn(SubForum $f) => $f->getId(), $results);

        self::assertSame(
            [
                '1786e163-059e-43c8-b1f7-50114772a96d',
                'c095e6af-422b-4bd0-b456-c16659d061dd',
                '05b19b0d-2751-42a9-aaae-9bb735461ff0',
                '96b8e569-9c77-4741-b58c-84d7318a2ebe',
                'e0af62d3-8e63-4550-805a-cabb1cfd7779',
            ],
            $results,
        );
    }
}
