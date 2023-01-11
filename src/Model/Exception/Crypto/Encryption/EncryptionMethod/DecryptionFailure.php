<?php

declare(strict_types=1);

namespace App\Model\Exception\Crypto\Encryption\EncryptionMethod;

use App\Model\Entity\Persistent;
use Exception;
use ReflectionClass;
use Throwable;
use function get_class;

class DecryptionFailure extends Exception
{
    public static function because(object $obj, Throwable $previous): self
    {
        $type = get_class($obj);
        $message = "Failed to decrypt object of type '$type'";
        if ($obj instanceof Persistent) {
            $class = new ReflectionClass($obj);

            $message = "Failed to decrypt {$class->getShortName()} [{$obj->getId()}]";
        }

        return new self("$message because {$previous->getMessage()}", 0, $previous);
    }
}
