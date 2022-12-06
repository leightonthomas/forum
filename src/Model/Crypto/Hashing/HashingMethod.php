<?php

declare(strict_types=1);

namespace App\Model\Crypto\Hashing;

use App\Model\Primitive\HashedString;
use ParagonIE\HiddenString\HiddenString;

interface HashingMethod
{
    public function hash(HiddenString $value): HashedString;
    public function verify(HiddenString $raw, HashedString $hash): bool;
}
