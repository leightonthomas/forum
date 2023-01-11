<?php

declare(strict_types=1);

namespace App\Model\Repository\Entity\Thread;

use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;

interface ThreadRepository
{
    /**
     * @param SubForum $forum
     * @param int $page
     * @param int $limit
     *
     * @return list<Thread>
     */
    public function getPageForSubForum(SubForum $forum, int $page, int $limit): array;
}
