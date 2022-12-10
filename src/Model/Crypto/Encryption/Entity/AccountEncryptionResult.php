<?php

declare(strict_types=1);

namespace App\Model\Crypto\Encryption\Entity;

use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;

class AccountEncryptionResult
{
    public function __construct(
        public readonly EncryptedString $username,
        public readonly HashedString $usernameBlindIndex,
        public readonly EncryptedString $emailAddress,
        public readonly HashedString $emailAddressFullBlindIndex,
    ) { }
}
