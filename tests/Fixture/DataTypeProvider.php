<?php

declare(strict_types=1);

namespace Tests\Fixture;

use BenTools\CartesianProduct\CartesianProduct;
use Generator;
use stdClass;

enum DataTypeProvider
{
    public static function dataTypes(): array
    {
        return [
            'string' => ['hello'],
            'int' => [1],
            'float' => [2.3],
            'bool (true)' => [true],
            'bool (false)' => [false],
            'null' => [null],
            'array' => [[]],
            'stdClass' => [new stdClass()],
        ];
    }

    public static function jsonDataTypes(): array
    {
        return [
            'string' => ['hello'],
            'int' => [1],
            'float' => [2.3],
            'bool (true)' => [true],
            'bool (false)' => [false],
            'null' => [null],
            'array' => [[]],
        ];
    }

    /**
     * @param array $dataSets
     * @param callable(array):array[] $callable
     *
     * @return Generator
     */
    public static function cartesianProduct(
        array $dataSets,
        callable $callable,
    ): Generator {
        $products = new CartesianProduct($dataSets);

        foreach ($products as $product) {
            yield $callable($product);
        }
    }
}
