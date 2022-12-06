<?php

declare(strict_types=1);

namespace Tests\Integration\Transformer\Controller\Public\V1\Account;

use App\Model\Exception\Transformer\TransformationFailed;
use App\Transformer\Controller\Public\V1\Account\RegistrationRequestTransformer;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Fixture\DataTypeProvider;
use function array_fill;
use function is_string;
use function join;
use function json_encode;

class RegistrationRequestTransformerTest extends KernelTestCase
{
    private RegistrationRequestTransformer $transformer;

    protected function setUp(): void
    {
        self::bootKernel(['env' => 'test']);

        $this->transformer = static::getContainer()->get(RegistrationRequestTransformer::class);
    }

    #[Test]
    #[DataProvider('notString')]
    public function itWillThrowIfRawRequestDataIsNotAString(mixed $value): void
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $request
            ->expects(self::once())
            ->method('getContent')
            ->willReturn($value)
        ;

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(
                ['There was a problem processing your request.'],
                $e->errors,
            );

            return;
        }

        self::fail('Did not throw.');
    }

    public static function notString(): Generator
    {
        foreach (DataTypeProvider::dataTypes() as $type => [$value]){
            if (is_string($value)) {
                continue;
            }

            yield $type => [$value];
        }
    }

    #[Test]
    public function itWillThrowIfInvalidJsonProvided(): void
    {
        $request = new Request([], [], [], [], [], [], '{');

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(
                ['Invalid JSON.'],
                $e->errors,
            );

            return;
        }

        self::fail('Did not throw.');
    }

    #[Test]
    #[DataProvider('invalidIds')]
    public function itWillThrowIfInvalidIdProvided(mixed $value, array $errors): void
    {
        $data = $this->getValidData();
        $data['id'] = $value;

        $request = new Request([], [], [], [], [], [], json_encode($data));

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(['id' => $errors], $e->errors);

            return;
        }

        self::fail('Did not throw.');
    }

    public static function invalidIds(): Generator
    {
        foreach (DataTypeProvider::jsonDataTypes() as $type => [$value]) {
            if (is_string($value)) {
                continue;
            }

            yield $type => [$value, ['This value must be of type string.']];
        }

        $factory = new UuidFactory();

        yield 'non-uuid' => ['hi', ['This value is invalid.']];
        yield 'uuid of wrong type' => [$factory->uuid1()->toString(), ['This value is invalid.']];
        yield 'uuid with extra' => ['6c0dc70e-4965-429b-a22e-7e51eb6a593fhi', ['This value is invalid.']];
    }

    #[Test]
    #[DataProvider('invalidUsernames')]
    public function itWillThrowIfInvalidUsernameProvided(mixed $value, array $errors): void
    {
        $data = $this->getValidData();
        $data['username'] = $value;

        $request = new Request([], [], [], [], [], [], json_encode($data));

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(['username' => $errors], $e->errors);

            return;
        }

        self::fail('Did not throw.');
    }

    public static function invalidUsernames(): Generator
    {
        foreach (DataTypeProvider::jsonDataTypes() as $type => [$value]) {
            if (is_string($value)) {
                continue;
            }

            yield $type => [$value, ['This value must be of type string.']];
        }

        yield 'invalid character #1' => ['hello     there', ['This value is invalid.']];
        yield 'invalid character #2' => ['hi🥲', ['This value is invalid.']];
        yield 'invalid character #3' => ['hi🤦🏽‍♂️', ['This value is invalid.']];
        yield 'too short #1' => ['a', ['This value is invalid.']];
        yield 'too short #2' => ['ab', ['This value is invalid.']];
        yield 'too long #1' => ['abcdeabcdeabcdeabcdeabcdeabcdea', ['This value is invalid.']];
        yield 'too long #2' => ['abcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcde', ['This value is invalid.']];
    }

    #[Test]
    #[DataProvider('invalidEmails')]
    public function itWillThrowIfInvalidEmailAddressProvided(mixed $value, array $errors): void
    {
        $data = $this->getValidData();
        $data['emailAddress'] = $value;

        $request = new Request([], [], [], [], [], [], json_encode($data));

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(['emailAddress' => $errors], $e->errors);

            return;
        }

        self::fail('Did not throw.');
    }

    public static function invalidEmails(): Generator
    {
        foreach (DataTypeProvider::jsonDataTypes() as $type => [$value]) {
            if (is_string($value)) {
                continue;
            }

            yield $type => [$value, ['This value must be of type string.']];
        }

        yield 'invalid email #1' => ['hello     there', ['This value must be a valid email address.']];
    }

    #[Test]
    #[DataProvider('invalidPasswords')]
    public function itWillThrowIfInvalidPasswordProvided(mixed $value, array $errors): void
    {
        $data = $this->getValidData();
        $data['password'] = $value;

        $request = new Request([], [], [], [], [], [], json_encode($data));

        try {
            $this->transformer->transform($request);
        } catch (TransformationFailed $e) {
            self::assertSame(['password' => $errors], $e->errors);

            return;
        }

        self::fail('Did not throw.');
    }

    public static function invalidPasswords(): Generator
    {
        foreach (DataTypeProvider::jsonDataTypes() as $type => [$value]) {
            if (is_string($value)) {
                continue;
            }

            yield $type => [$value, ['This value must be of type string.']];
        }

        yield 'too short #1' => ['😺', ['This value is too short.']];
        yield 'too short #2' => ['a', ['This value is too short.']];
        yield 'too long #1' => [
            join('', array_fill(0, 129, 'a')),
            ['This value is too long.'],
        ];
        yield 'too long #2' => [
            join('', array_fill(0, 1000, 'a')),
            ['This value is too long.'],
        ];
    }

    #[Test]
    #[DataProvider('validDataProvider')]
    public function itWillNotThrowOnValidData(array $data): void
    {
        $this->expectNotToPerformAssertions();

        $request = new Request([], [], [], [], [], [], json_encode($data));

        $this->transformer->transform($request);
    }

    public static function validDataProvider(): Generator
    {
        return DataTypeProvider::cartesianProduct(
            [
                'id' => [
                    'ffcbf2e7-5217-4e4d-a022-766a57a9b4a7',
                    '23bdd73b-e1cb-4636-a594-78708ce49399',
                    Uuid::uuid4(),
                ],
                'username' => [
                    'abc',
                    'some_long_name',
                    'a1-_B',
                    join('', array_fill(0, 30, 'a')),
                ],
                // we don't need to test _all_ edge cases here, the email checker should do that
                'emailAddress' => [
                    'test@example.com',
                    'test+2@example.com',
                ],
                'password' => [
                    'asdfasdfasdf',
                    '🕵🏽‍♀️👨🏾‍🦰👩🏻‍🦰hello123Ù',
                    join('', array_fill(0, 127, 'a')),
                    join('', array_fill(0, 128, 'a')),
                ],
            ],
            fn(array $product) => [
                [
                    'id' => $product['id'],
                    'username' => $product['username'],
                    'emailAddress' => $product['emailAddress'],
                    'password' => $product['password'],
                ],
            ],
        );
    }

    private function getValidData(): array
    {
        return [
            'id' => 'b3fe3614-fd5b-4e69-80dc-24310180cdce',
            'username' => 'bob',
            'emailAddress' => 'test@example.com',
            'password' => 'some fake password',
        ];
    }
}
