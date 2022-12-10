<?php

declare(strict_types=1);

namespace App\Controller\Public\V1\Account;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Model\Attribute\Controller\JsonRoute;
use App\Model\Crypto\Hashing\HashingMethod;
use App\Model\Entity\Account;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\EncryptionFailure;
use App\Model\Exception\Repository\FailedToPersist;
use App\Model\Exception\Transformer\TransformationFailed;
use App\Model\Exception\Transformer\TransformerMisconfiguration;
use App\Model\Repository\Entity\AccountRepository;
use App\Transformer\Controller\Public\V1\Account\RegistrationRequestTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RegistrationRequestTransformer $requestTransformer,
        private readonly AccountEncryptor $encryptor,
        private readonly HashingMethod $hasher,
        private readonly AccountRepository $accountRepository,
    ) { }

    #[Route(path: '/public/v1/account', name: 'public_register', methods: ['POST'])]
    #[JsonRoute]
    public function __invoke(Request $request): Response
    {
        try {
            $registrationRequest = $this->requestTransformer->transform($request);
        } catch (TransformationFailed $e) {
            return new JsonResponse(['errors' => $e->errors], Response::HTTP_BAD_REQUEST);
        } catch (TransformerMisconfiguration $e) {
            $this->logger->emergency(
                "Misconfiguration of registration request transformer; registrations are broken - {$e->getMessage()}",
            );

            return new JsonResponse(
                ['errors' => ['There was a problem processing your request. Please try again later.']],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        try {
            $encryptedAccountData = $this->encryptor->encrypt(
                $registrationRequest->id,
                $registrationRequest->username,
                $registrationRequest->emailAddress,
            );
        } catch (EncryptionFailure $e) {
            $this->logger->critical("Failed to encrypt data in new user registration: {$e->getMessage()}");

            return new JsonResponse(
                ['errors' => ['There was a problem processing your request. Please try again later.']],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        if (
            $this->accountRepository->exists(
                $registrationRequest->id,
                $encryptedAccountData->usernameBlindIndex,
                $encryptedAccountData->emailAddressFullBlindIndex,
            )
        ) {
            return new JsonResponse(
                ['errors' => ['There is already an account with the provided id, email address, or username.']],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $account = new Account(
            $registrationRequest->id,
            $encryptedAccountData->username,
            $encryptedAccountData->usernameBlindIndex,
            $encryptedAccountData->emailAddress,
            $encryptedAccountData->emailAddressFullBlindIndex,
            $this->hasher->hash($registrationRequest->password),
            ['ROLE_USER'],
        );

        try {
            $this->accountRepository->save($account);
        } catch (FailedToPersist $e) {
            $this->logger->critical(
                "Failed to save new Account in new user registration: {$e->getMessage()}",
            );

            return new JsonResponse(
                ['errors' => ['There was a problem processing your request. Please try again later.']],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return new JsonResponse(['id' => $account->getId()], Response::HTTP_CREATED);
    }
}
