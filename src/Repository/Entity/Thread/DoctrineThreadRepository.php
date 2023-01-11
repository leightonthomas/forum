<?php

declare(strict_types=1);

namespace App\Repository\Entity\Thread;

use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use App\Model\Repository\Entity\Thread\ThreadRepository;
use Doctrine\ORM\EntityRepository;
use function max;
use function min;

/**
 * @extends EntityRepository<Thread>
 */
class DoctrineThreadRepository extends EntityRepository implements ThreadRepository
{
    public function getPageForSubForum(SubForum $forum, int $page, int $limit): array
    {
        $limit = max(1, min(100, $limit));
        $page = max(1, min(10000, $page));

        /** @var list<Thread> $result */
        $result = $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.forum = :forum')
            ->setParameter('forum', $forum->getId())
            ->orderBy('t.createdAt', 'DESC') // newest first
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
