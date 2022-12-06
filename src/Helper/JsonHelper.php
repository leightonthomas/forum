<?php

declare(strict_types=1);

namespace App\Helper;

use JsonException;
use function json_decode;
use const JSON_THROW_ON_ERROR;

/**
 * Standardise JSON functions (depth, throw on errors, arrays instead of objects, etc)
 */
enum JsonHelper
{
    /**
     * @param string $json
     * @param int $depth
     *
     * @return array|string|float|int|bool|null
     *
     * @throws JsonException
     */
    public static function decode(string $json, int $depth = 10): array|string|float|int|bool|null
    {
        return json_decode($json, true, $depth, JSON_THROW_ON_ERROR);
    }
}
