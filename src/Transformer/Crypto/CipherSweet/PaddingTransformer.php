<?php

declare(strict_types=1);

namespace App\Transformer\Crypto\CipherSweet;

use ParagonIE\CipherSweet\Contract\TransformationInterface;
use function str_pad;

class PaddingTransformer implements TransformationInterface
{
    public function __construct(
        private readonly int $length,
    ) { }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType this is the intended way to implement the interface
     *
     * @param string $input
     *
     * @return string
     */
    public function __invoke($input): string
    {
        return str_pad($input, $this->length, '_');
    }
}
