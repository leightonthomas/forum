<?php

declare(strict_types=1);

namespace App\Validator\Checker;

use App\Validator\Rule\IsEmailAddress;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use LeightonThomas\Validation\Checker\Checker;
use LeightonThomas\Validation\Rule\Rule;
use LeightonThomas\Validation\ValidationResult;

class IsEmailAddressChecker implements Checker
{
    private readonly EmailValidator $validator;

    public function __construct()
    {
        $this->validator = new EmailValidator();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value
     * @param IsEmailAddress $rule
     */
    public function check($value, Rule $rule, ValidationResult $result): void
    {
        if ($this->validator->isValid($value, new NoRFCWarningsValidation())) {
            return;
        }

        $result->addError($rule->getMessages()[IsEmailAddress::ERR_INVALID]);
    }

    public function canCheck(): array
    {
        return [IsEmailAddress::class];
    }
}
