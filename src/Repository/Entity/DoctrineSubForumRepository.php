<?php

declare(strict_types=1);

namespace App\Repository\Entity;

use App\Model\Repository\Entity\SubForumRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineSubForumRepository extends EntityRepository implements SubForumRepository
{
    public function list(): array
    {
        return $this
            ->createQueryBuilder('sf')
            ->orderBy('sf.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
