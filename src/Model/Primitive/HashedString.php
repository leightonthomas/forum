<?php

declare(strict_types=1);

namespace App\Model\Primitive;

use Stringable;

/**
 * Wraps a regular string to make it clear it's a hashed value, and to avoid non-hashed strings in places
 * where they are expected.
 */
class HashedString implements Stringable
{
    public function __construct(
        /** The hash itself. */
        public readonly string $value,
    ) { }

    public function __toString(): string
    {
        return $this->value;
    }
}
