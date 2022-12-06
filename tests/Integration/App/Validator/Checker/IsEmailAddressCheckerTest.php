<?php

declare(strict_types=1);

namespace Tests\Integration\App\Validator\Checker;

use App\Validator\Checker\IsEmailAddressChecker;
use App\Validator\Rule\IsEmailAddress;
use LeightonThomas\Validation\ValidationResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function sprintf;
use function str_repeat;

class IsEmailAddressCheckerTest extends TestCase
{
    private IsEmailAddressChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new IsEmailAddressChecker();
    }

    #[Test]
    #[DataProvider('validEmails')]
    public function itWillAddNoErrorsIfValidDataProvided(string $emailAddress): void
    {
        $validationResult = new ValidationResult($emailAddress);

        $this->checker->check($emailAddress, new IsEmailAddress(), $validationResult);

        self::assertTrue($validationResult->isValid());
        self::assertEmpty($validationResult->getErrors());
    }

    // @see https://github.com/egulias/EmailValidator/blob/3.x/tests/EmailValidator/Validation/RFCValidationTest.php
    public static function validEmails(): array
    {
        return [
            ['â@iana.org'],
            ['fabien@symfony.com'],
            ['example@example.co.uk'],
            ['fabien_potencier@example.fr'],
            ['fab\'ien@symfony.com'],
            ['fab\ ien@symfony.com'],
            ['fabien+a@symfony.com'],
            ['exampl=e@example.com'],
            ['инфо@письмо.рф'],
            ['müller@möller.de'],
            ["1500111@профи-инвест.рф"],
            [sprintf('example@%s.com', str_repeat('ъ', 40))],
        ];
    }

    #[Test]
    #[DataProvider('invalidEmails')]
    public function itWillAddErrorsIfInvalidDataProvided(string $emailAddress): void
    {
        $validationResult = new ValidationResult($emailAddress);

        $this->checker->check($emailAddress, new IsEmailAddress(), $validationResult);

        self::assertFalse($validationResult->isValid());
        self::assertSame(['This value must be a valid email address.'], $validationResult->getErrors());
    }

    // @see https://github.com/egulias/EmailValidator/blob/3.x/tests/EmailValidator/Validation/RFCValidationTest.php
    public static function invalidEmails(): array
    {
        return [
            ['user  name@example.com'],
            ['user   name@example.com'],
            ['example.@example.co.uk'],
            ['example@example@example.co.uk'],
            ['(test_exampel@example.fr'],
            ['example(example]example@example.co.uk'],
            ['.example@localhost'],
            ['ex\ample@localhost'],
            ['user name@example.com'],
            ['usern,ame@example.com'],
            ['user[na]me@example.com'],
            ['"""@iana.org'],
            ['"\"@iana.org'],
            ['"test"test@iana.org'],
            ['"test""test"@iana.org'],
            ['"test"."test"@iana.org'],
            ['"test".test@iana.org'],
            ['"test"' . chr(0) . '@iana.org'],
            ['"test\"@iana.org'],
            [chr(226) . '@iana.org'],
            ['\r\ntest@iana.org'],
            ['\r\n test@iana.org'],
            ['\r\n \r\ntest@iana.org'],
            ['\r\n \r\ntest@iana.org'],
            ['\r\n \r\n test@iana.org'],
            ['test;123@foobar.com'],
            ['examp║le@symfony.com'],
            ['0'],
            ['a5aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@example.com'],
            ['example@example'],
            ['example @invalid.example.com'],
            ['example(examplecomment)@invalid.example.com'],
            ["\"\t\"@invalid.example.com"],
            ["\"\r\"@invalid.example.com"],
            ['"example"@invalid.example.com'],
            ['too_long_localpart_too_long_localpart_too_long_localpart_too_long_localpart@invalid.example.com'],
        ];
    }
}
