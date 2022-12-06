<?php

declare(strict_types=1);

namespace App\Model\Exception\Crypto\Encryption\EncryptionMethod;

use Exception;
use Throwable;

class EncryptionFailure extends Exception
{
    public static function because(Throwable $previous): self
    {
        return new self($previous->getMessage(), 0, $previous);
    }
}
