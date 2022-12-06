<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controller\Public\V1\Account;

use App\Controller\Public\V1\Account\RegistrationController;
use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Helper\JsonHelper;
use App\Model\Controller\Public\V1\Account\RegistrationRequest;
use App\Model\Crypto\Encryption\Entity\AccountEncryptionResult;
use App\Model\Crypto\Hashing\HashingMethod;
use App\Model\Entity\Account;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\EncryptionFailure;
use App\Model\Exception\Repository\FailedToPersist;
use App\Model\Exception\Transformer\TransformationFailed;
use App\Model\Exception\Transformer\TransformerMisconfiguration;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use App\Model\Repository\Entity\AccountRepository;
use App\Transformer\Controller\Public\V1\Account\RegistrationRequestTransformer;
use Exception;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends TestCase
{
    private MockObject&LoggerInterface $logger;
    private MockObject&RegistrationRequestTransformer $requestTransformer;
    private MockObject&AccountEncryptor $encryptor;
    private MockObject&HashingMethod $hasher;
    private MockObject&AccountRepository $accountRepository;
    private RegistrationController $controller;

    protected function setUp(): void
    {
        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->requestTransformer = $this
            ->getMockBuilder(RegistrationRequestTransformer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->encryptor = $this
            ->getMockBuilder(AccountEncryptor::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->hasher = $this
            ->getMockBuilder(HashingMethod::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->accountRepository = $this
            ->getMockBuilder(AccountRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->controller = new RegistrationController(
            $this->logger,
            $this->requestTransformer,
            $this->encryptor,
            $this->hasher,
            $this->accountRepository,
        );
    }

    #[Test]
    public function itWillReturnBadRequestIfTransformationFailure(): void
    {
        $request = new Request();

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willThrowException(new TransformationFailed(['oh no']))
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['oh no']],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    public function itWillReturnServerErrorAndLogIfTransformerMisconfigured(): void
    {
        $request = new Request();

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willThrowException(new TransformerMisconfiguration('oh no'))
        ;

        $this->logger
            ->expects(self::once())
            ->method('emergency')
            ->with(self::matchesRegularExpression('/^Misconfiguration of registration request transformer.+oh no/'))
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['There was a problem processing your request. Please try again later.']],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    public function itWillReturnServerErrorAndLogIfEncryptionFails(): void
    {
        $request = new Request();
        $registrationRequest = new RegistrationRequest(
            'a',
            'b',
            new HiddenString('c'),
            new HiddenString('d'),
        );

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willReturn($registrationRequest)
        ;

        $this->encryptor
            ->expects(self::once())
            ->method('encrypt')
            ->with('a', $registrationRequest->emailAddress)
            ->willThrowException(new EncryptionFailure('oh no'))
        ;

        $this->logger
            ->expects(self::once())
            ->method('critical')
            ->with(self::matchesRegularExpression('/^Failed to encrypt data.+oh no/'))
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['There was a problem processing your request. Please try again later.']],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    public function itWillReturnBadRequestIfAccountAlreadyExistsWithGivenData(): void
    {
        $request = new Request();
        $registrationRequest = new RegistrationRequest(
            'a',
            'b',
            new HiddenString('c'),
            new HiddenString('d'),
        );
        $encryptedData = new AccountEncryptionResult(
            new EncryptedString('encrypted c'),
            new HashedString('email blind index'),
        );

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willReturn($registrationRequest)
        ;

        $this->encryptor
            ->expects(self::once())
            ->method('encrypt')
            ->with('a', $registrationRequest->emailAddress)
            ->willReturn($encryptedData)
        ;

        $this->accountRepository
            ->expects(self::once())
            ->method('exists')
            ->with('a', 'b', $encryptedData->emailAddressFullBlindIndex)
            ->willReturn(true)
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['There is already an account with the provided id, email address, or username.']],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    public function itWillReturnServerErrorIfNewAccountFailsToPersist(): void
    {
        $request = new Request();
        $registrationRequest = new RegistrationRequest(
            'a',
            'b',
            new HiddenString('c'),
            new HiddenString('d'),
        );
        $encryptedData = new AccountEncryptionResult(
            new EncryptedString('encrypted c'),
            new HashedString('email blind index'),
        );

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willReturn($registrationRequest)
        ;

        $this->encryptor
            ->expects(self::once())
            ->method('encrypt')
            ->with('a', $registrationRequest->emailAddress)
            ->willReturn($encryptedData)
        ;

        $this->accountRepository
            ->expects(self::once())
            ->method('exists')
            ->with('a', 'b', $encryptedData->emailAddressFullBlindIndex)
            ->willReturn(false)
        ;

        $this->hasher
            ->expects(self::once())
            ->method('hash')
            ->with($registrationRequest->password)
            ->willReturn(new HashedString('hashed d'))
        ;

        $this->accountRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(Account::class))
            ->willThrowException(FailedToPersist::onFlush(new Exception('oh no')))
        ;

        $this->logger
            ->expects(self::once())
            ->method('critical')
            ->with(self::matchesRegularExpression('/^Failed to save new Account.+oh no/'))
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['There was a problem processing your request. Please try again later.']],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    public function itWillReturnCreatedWithIdOnSuccess(): void
    {
        $request = new Request();
        $registrationRequest = new RegistrationRequest(
            'a',
            'b',
            new HiddenString('c'),
            new HiddenString('d'),
        );
        $encryptedData = new AccountEncryptionResult(
            new EncryptedString('encrypted c'),
            new HashedString('email blind index'),
        );

        $this->requestTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($request)
            ->willReturn($registrationRequest)
        ;

        $this->encryptor
            ->expects(self::once())
            ->method('encrypt')
            ->with('a', $registrationRequest->emailAddress)
            ->willReturn($encryptedData)
        ;

        $this->accountRepository
            ->expects(self::once())
            ->method('exists')
            ->with('a', 'b', $encryptedData->emailAddressFullBlindIndex)
            ->willReturn(false)
        ;

        $this->hasher
            ->expects(self::once())
            ->method('hash')
            ->with($registrationRequest->password)
            ->willReturn(new HashedString('hashed d'))
        ;

        $this->accountRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Account $account): bool {
                        self::assertSame('a', $account->getId());
                        self::assertSame('b', $account->getUsername());
                        self::assertSame('encrypted c', $account->getEmailAddress()->value);
                        self::assertSame('hashed d', $account->getPassword()->value);
                        self::assertSame(['ROLE_USER'], $account->getClaims());

                        return true;
                    },
                ),
            )
        ;

        $response = ($this->controller)($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame(
            ['id' => 'a'],
            JsonHelper::decode($response->getContent()),
        );
    }
}
