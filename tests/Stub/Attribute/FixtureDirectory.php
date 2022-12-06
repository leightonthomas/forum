<?php

declare(strict_types=1);

namespace Tests\Stub\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FixtureDirectory
{
    /**
     * @param string $directory the directory in which to look for fixtures relative to the base fixture directory
     */
    public function __construct(
        public readonly string $directory,
    ) { }
}
