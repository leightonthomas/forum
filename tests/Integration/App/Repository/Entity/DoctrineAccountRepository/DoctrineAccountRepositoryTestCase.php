<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineAccountRepository;

use App\Repository\Entity\DoctrineAccountRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineAccountRepositoryTestCase extends KernelTestCase
{
    protected DoctrineAccountRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel(['env' => 'test']);

        $this->repository = static::getContainer()->get(DoctrineAccountRepository::class);
    }
}
