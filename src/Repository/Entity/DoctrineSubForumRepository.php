<?php

declare(strict_types=1);

namespace App\Repository\Entity;

use App\Model\Entity\SubForum;
use App\Model\Repository\Entity\SubForumRepository;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

/**
 * @extends EntityRepository<SubForum>
 */
class DoctrineSubForumRepository extends EntityRepository implements SubForumRepository
{
    public function list(): array
    {
        /** @var list<SubForum> $result */
        $result = $this
            ->createQueryBuilder('sf')
            ->orderBy('sf.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function findById(string $id): ?SubForum
    {
        if (! Uuid::isValid($id)) {
            return null;
        }

        /** @var SubForum|null $result */
        $result = $this->find($id);

        return $result;
    }
}
