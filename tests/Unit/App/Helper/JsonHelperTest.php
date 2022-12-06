<?php

declare(strict_types=1);

namespace Tests\Unit\App\Helper;

use App\Helper\JsonHelper;
use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    #[Test]
    public function itWillThrowIfJsonError(): void
    {
        $this->expectException(JsonException::class);

        JsonHelper::decode('{');
    }

    #[Test]
    #[DataProvider('decodeProvider')]
    public function itWillDecodeCorrectly(string $json, mixed $expected): void
    {
        $result = JsonHelper::decode($json);

        self::assertSame($expected, $result);
    }

    public static function decodeProvider(): array
    {
        return [
            ['[]', []],
            ['{}', []],
            ['{"a":"b"}', ['a' => 'b']],
            ['1', 1],
            ['1.2', 1.2],
            ['"1"', "1"],
            ['true', true],
            ['false', false],
            ['null', null],
        ];
    }
}
