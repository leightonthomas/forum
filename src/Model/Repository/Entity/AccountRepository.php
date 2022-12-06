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
     * @param string $username
     * @param HashedString $emailAddressBlindIndex
     *
     * @return bool
     */
    public function exists(string $id, string $username, HashedString $emailAddressBlindIndex): bool;

    /**
     * Persist & flush the given {@see Account}.
     *
     * @param Account $account
     *
     * @throws FailedToPersist
     */
    public function save(Account $account): void;
}
