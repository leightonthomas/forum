<?php

declare(strict_types=1);

namespace Tests\Stub\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Fixture
{
    public function __construct(
        /**
         * The fixture file relative to the {@see FixtureDirectory} path on the class.
         *
         * Start with a `.` to specify an explicit fixture path that does _not_ respect the {@see FixtureDirectory}.
         */
        public readonly string $fixtureName,
    ) { }
}
