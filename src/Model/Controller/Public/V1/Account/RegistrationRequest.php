<?php

declare(strict_types=1);

namespace App\Model\Controller\Public\V1\Account;

use ParagonIE\HiddenString\HiddenString;

final class RegistrationRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly HiddenString $emailAddress,
        public readonly HiddenString $password,
    ) { }
}
