<?php

declare(strict_types=1);

namespace App\Model\Repository\Entity;

use App\Model\Entity\Account;
use App\Model\Exception\Repository\FailedToPersist;
use App\Model\Primitive\HashedString;

interface AccountRepository
{
    /**
     * Check whether an account already exists with ONE OF the provided values.
     *
     * @param string $id
     * @param HashedString $usernameBlindIndex
     * @param HashedString $emailAddressBlindIndex
     *
     * @return bool
     */
    public function exists(string $id, HashedString $usernameBlindIndex, HashedString $emailAddressBlindIndex): bool;

    /**
     * Persist & flush the given {@see Account}.
     *
     * @param Account $account
     *
     * @throws FailedToPersist
     */
    public function save(Account $account): void;

    public function findById(string $id): ?Account;
}
