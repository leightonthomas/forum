<?php

declare(strict_types=1);

namespace App\Model\Primitive;

use Stringable;

/**
 * Wraps a regular string to make it clear it's an encrypted value, and to avoid non-encrypted strings in places
 * where they are expected.
 */
class EncryptedString implements Stringable
{
    public function __construct(
        /** The encrypted value. */
        public readonly string $value,
    ) { }

    public function __toString(): string
    {
        return $this->value;
    }
}
