<?php

declare(strict_types=1);

namespace App\Validator\Rule;

use LeightonThomas\Validation\Rule\Rule;

/**
 * @extends Rule<string, string>
 */
class IsEmailAddress extends Rule
{
    public const ERR_INVALID = 0;
    public const ERR_NOT_STRING = 1;

    public function __construct()
    {
        $this->messages = [
            self::ERR_INVALID => 'This value must be a valid email address.',
            self::ERR_NOT_STRING => 'This value must be a string.',
        ];
    }
}
