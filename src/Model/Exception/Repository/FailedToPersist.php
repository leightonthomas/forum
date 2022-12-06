<?php

declare(strict_types=1);

namespace App\Model\Exception\Repository;

use App\Model\Entity\Persistent;
use Doctrine\Common\Util\ClassUtils;
use Exception;
use Throwable;
use function sprintf;

class FailedToPersist extends Exception
{
    public static function onPersist(Persistent $obj, ?Throwable $prev = null): self
    {
        return new self(
            sprintf(
                "Failed to persist %s [%s]%s",
                ClassUtils::getClass($obj),
                $obj->getId(),
                ($prev === null) ? '' : " because {$prev->getMessage()}",
            ),
            $prev,
        );
    }

    public static function onFlush(?Throwable $prev = null): self
    {
        return new self(
            sprintf(
                "Failed to flush entities%s",
                ($prev === null) ? '' : " because {$prev->getMessage()}",
            ),
            $prev,
        );
    }

    private function __construct(string $message, ?Throwable $prev = null)
    {
        parent::__construct($message, 0, $prev);
    }
}
