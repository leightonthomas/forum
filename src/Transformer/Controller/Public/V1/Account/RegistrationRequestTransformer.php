<?php

declare(strict_types=1);

namespace App\Transformer\Controller\Public\V1\Account;

use App\Helper\JsonHelper;
use App\Model\Controller\Public\V1\Account\RegistrationRequest;
use App\Model\Exception\Transformer\TransformationFailed;
use App\Model\Exception\Transformer\TransformerMisconfiguration;
use App\Validator\Rule\IsEmailAddress;
use JsonException;
use LeightonThomas\Validation\Exception\NoCheckersRegistered;
use LeightonThomas\Validation\Rule\Arrays\IsDefinedArray;
use LeightonThomas\Validation\Rule\Combination\Compose;
use LeightonThomas\Validation\Rule\Scalar\Strings\IsString;
use LeightonThomas\Validation\Rule\Scalar\Strings\Length;
use LeightonThomas\Validation\Rule\Scalar\Strings\Regex;
use LeightonThomas\Validation\ValidatorFactory;
use ParagonIE\HiddenString\HiddenString;
use Symfony\Component\HttpFoundation\Request;
use function is_string;
use function strtolower;

class RegistrationRequestTransformer
{
    public function __construct(
        private readonly ValidatorFactory $validatorFactory,
    ) { }

    /**
     * @param Request $request
     *
     * @return RegistrationRequest
     *
     * @throws TransformationFailed
     * @throws TransformerMisconfiguration
     */
    public function transform(Request $request): RegistrationRequest
    {
        $rawData = $request->getContent();
        if (! is_string($rawData)) {
            throw new TransformationFailed(['There was a problem processing your request.']);
        }

        try {
            /** @psalm-suppress MixedAssignment */
            $decodedData = JsonHelper::decode($rawData);
        } catch (JsonException) {
            throw new TransformationFailed(['Invalid JSON.']);
        }

        $passwordLength = new Length(12, 128);
        $passwordLength->setMessage(Length::ERR_TOO_LONG, 'This value is too long.');
        $passwordLength->setMessage(Length::ERR_TOO_SHORT, 'This value is too short.');

        $validation = (
            IsDefinedArray::of(
                'id',
                Compose::from(new IsString())
                    ->and(new Regex('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i')),
            )
            ->and(
                'username',
                Compose::from(new IsString())
                    ->and(new Regex('/^[a-z0-9_\-]{3,30}$/i'))
            )
            ->and(
                'emailAddress',
                Compose::from(new IsString())
                    ->and(new IsEmailAddress())
            )
            ->and(
                'password',
                Compose::from(new IsString())
                    ->and($passwordLength)
            )
            ->withNoOtherKeys()
        );

        try {
            $validationResult = $this->validatorFactory->create($validation)->validate($decodedData);
        } catch (NoCheckersRegistered) {
            throw new TransformerMisconfiguration(
                "There are no validation checkers registered, cannot transform/validate",
            );
        }

        if (! $validationResult->isValid()) {
            throw new TransformationFailed($validationResult->getErrors());
        }

        $validatedData = $validationResult->getValue();

        /** @var string $id */
        $id = $validatedData['id'];
        /** @var string $username */
        $username = $validatedData['username'];
        /** @var string $emailAddress */
        $emailAddress = $validatedData['emailAddress'];
        /** @var string $password */
        $password = $validatedData['password'];

        return new RegistrationRequest(
            $id,
            new HiddenString($username),
            new HiddenString(strtolower($emailAddress)),
            new HiddenString($id . $password),
        );
    }
}
