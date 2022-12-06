<?php

declare(strict_types=1);

namespace App\Model\Exception\Transformer;

use Exception;

class TransformationFailed extends Exception
{
    public function __construct(
        public readonly array $errors,
    ) {
        parent::__construct("Transformation failed.");
    }
}
