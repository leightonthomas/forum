<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineThreadRepository;

use App\Repository\Entity\Thread\DoctrineThreadRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineThreadRepositoryTestCase extends KernelTestCase
{
    protected DoctrineThreadRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel(['env' => 'test']);

        $this->repository = static::getContainer()->get(DoctrineThreadRepository::class);
    }
}
