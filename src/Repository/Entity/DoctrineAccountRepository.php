<?php

declare(strict_types=1);

namespace App\Repository\Entity;

use App\Model\Entity\Account;
use App\Model\Exception\Repository\FailedToPersist;
use App\Model\Primitive\HashedString;
use App\Model\Repository\Entity\AccountRepository;
use Doctrine\ORM\EntityRepository;
use Throwable;

class DoctrineAccountRepository extends EntityRepository implements AccountRepository
{
    public function exists(string $id, string $username, HashedString $emailAddressBlindIndex): bool
    {
        $result = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.id = :id')
            ->orWhere('a.username = :username')
            ->orWhere('a.emailAddressBlindIndex = :email')
            ->setParameter('id', $id)
            ->setParameter('username', $username)
            ->setParameter('email', $emailAddressBlindIndex)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result !== null;
    }

    public function save(Account $account): void
    {
        try {
            $this->getEntityManager()->persist($account);
            $this->getEntityManager()->flush();
        } catch (Throwable $e) {
            throw FailedToPersist::onPersist($account, $e);
        }
    }
}
