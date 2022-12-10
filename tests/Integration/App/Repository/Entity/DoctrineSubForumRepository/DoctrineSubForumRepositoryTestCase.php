<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineSubForumRepository;

use App\Repository\Entity\DoctrineSubForumRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineSubForumRepositoryTestCase extends KernelTestCase
{
    protected DoctrineSubForumRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel(['env' => 'test']);

        $this->repository = static::getContainer()->get(DoctrineSubForumRepository::class);
    }
}
