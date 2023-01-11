<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
