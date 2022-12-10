<?php

declare(strict_types=1);

namespace App\Model\Crypto\Encryption\Entity;

use ParagonIE\HiddenString\HiddenString;

class AccountDecryptionResult
{
    public function __construct(
        public readonly HiddenString $username,
        public readonly HiddenString $emailAddress,
    ) { }
}
