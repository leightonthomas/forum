<?php

declare(strict_types=1);

namespace App\Model\Controller\Public\V1\Account;

use ParagonIE\HiddenString\HiddenString;

readonly class RegistrationRequest
{
    public function __construct(
        public readonly string $id,
        public readonly HiddenString $username,
        public readonly HiddenString $emailAddress,
        public readonly HiddenString $password,
    ) { }
}
