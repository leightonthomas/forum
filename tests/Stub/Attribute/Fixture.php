<?php

declare(strict_types=1);

namespace Tests\Stub\Attribute;

use Attribute;

#[Attribute]
class Fixture
{
    public function __construct(
        public readonly string $fixtureName,
    ) { }
}
