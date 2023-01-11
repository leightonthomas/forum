<?php

declare(strict_types=1);

namespace App\Model\Repository\Entity;

use App\Model\Entity\SubForum;

interface SubForumRepository
{
    /**
     * @return list<SubForum>
     */
    public function list(): array;

    public function findById(string $id): ?SubForum;
}
