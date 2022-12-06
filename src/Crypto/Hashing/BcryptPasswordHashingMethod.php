<?php

declare(strict_types=1);

namespace App\Crypto\Hashing;

use App\Model\Crypto\Hashing\HashingMethod;
use App\Model\Primitive\HashedString;
use ParagonIE\HiddenString\HiddenString;
use function password_hash;
use function password_verify;
use const PASSWORD_BCRYPT;

class BcryptPasswordHashingMethod implements HashingMethod
{
    public function hash(HiddenString $value): HashedString
    {
        return new HashedString(password_hash($value->getString(), PASSWORD_BCRYPT, ['cost' => 13]));
    }

    public function verify(HiddenString $raw, HashedString $hash): bool
    {
        return password_verify($raw->getString(), $hash->value);
    }
}
