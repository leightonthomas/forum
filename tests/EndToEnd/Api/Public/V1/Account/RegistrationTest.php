<?php

declare(strict_types=1);

namespace Tests\EndToEnd\Api\Public\V1\Account;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Model\Entity\Account;
use App\Model\Repository\Entity\AccountRepository;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\EndToEnd\Api\ApiTestCase;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('/EndToEnd/Api/Public/V1/Account')]
class RegistrationTest extends ApiTestCase
{
    #[Test]
    #[Fixture('./Integration/empty.php')]
    public function itWillCreateAnAccountIfValidDataProvided(): void
    {
        $data = [
            'id' => '90632a8b-84a8-43ee-a5d7-3b95a18675fc',
            'username' => 'Bob',
            'emailAddress' => 'test@example.com',
            'password' => 'test!12345354',
        ];

        $response = $this->post('/public/v1/account', $data)['response'];

        self::assertJsonResponse(
            Response::HTTP_CREATED,
            ['id' => '90632a8b-84a8-43ee-a5d7-3b95a18675fc'],
            $response,
        );

        $account = self::getContainer()
            ->get(AccountRepository::class)
            ->findById('90632a8b-84a8-43ee-a5d7-3b95a18675fc')
        ;

        self::assertInstanceOf(Account::class, $account);
        self::assertSame('Bob', $account->getUsername());
        self::assertSame('90632a8b-84a8-43ee-a5d7-3b95a18675fc', $account->getId());

        $accountEncryptor = self::getContainer()->get(AccountEncryptor::class);
        $passwordHasher = self::getContainer()->get('crypto.hashing.entity.account.password_hashing_method');

        $decryptedAccountData = $accountEncryptor->decrypt($account);

        self::assertSame('test@example.com', $decryptedAccountData->emailAddress->getString());
        self::assertTrue(
            $passwordHasher->verify(
                new HiddenString('90632a8b-84a8-43ee-a5d7-3b95a18675fctest!12345354'),
                $account->getPassword(),
            ),
        );
    }

    #[Test]
    #[Fixture('./Integration/empty.php')]
    public function itWillReturnBadRequestIfInvalidDataProvided(): void
    {
        $data = [
            'id' => '90632a8b-84a8-43ee-a5d7-3b95a18675fc',
            'username' => 'Bob',
            'emailAddress' => 'test@example.com',
            'password' => 'too short',
        ];

        $response = $this->post('/public/v1/account', $data)['response'];

        self::assertJsonResponse(
            Response::HTTP_BAD_REQUEST,
            ['errors' => ['password' => ['This value is too short.']]],
            $response,
        );
    }

    #[Test]
    #[DataProvider('alreadyExistingDataProvider')]
    #[Fixture('existing.php')]
    public function itWillReturnBadRequestIfAccountAlreadyExistsWithGivenData(array $data): void
    {
        $response = $this->post('/public/v1/account', $data)['response'];

        self::assertJsonResponse(
            Response::HTTP_BAD_REQUEST,
            ['errors' => ['There is already an account with the provided id, email address, or username.']],
            $response,
        );
    }

    public static function alreadyExistingDataProvider(): array
    {
        return [
            'existing id' => [
                [
                    'id' => '4231002c-a796-41aa-90b8-947d12a49114',
                    'username' => 'someoneElse',
                    'emailAddress' => 'test@example.com',
                    'password' => 'test!!!123456',
                ],
            ],
            'existing username' => [
                [
                    'id' => '5d0707dd-3b5d-4f7b-af59-96e79137d3c7',
                    'username' => 'bob',
                    'emailAddress' => 'test@example.com',
                    'password' => 'test!!!123456',
                ],
            ],
            'existing email #1' => [
                [
                    'id' => '5d0707dd-3b5d-4f7b-af59-96e79137d3c7',
                    'username' => 'test',
                    'emailAddress' => 'bob@example.com',
                    'password' => 'test!!!123456',
                ],
            ],
            'existing email #2 - case makes no difference' => [
                [
                    'id' => '5d0707dd-3b5d-4f7b-af59-96e79137d3c7',
                    'username' => 'test',
                    'emailAddress' => 'bOb@ExAmPlE.cOm',
                    'password' => 'test!!!123456',
                ],
            ],
        ];
    }
}
