<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controller\Public\V1\SubForum;

use App\Controller\Public\V1\SubForum\ViewController;
use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Crypto\Encryption\Entity\Thread\ThreadEncryptor;
use App\Helper\JsonHelper;
use App\Model\Crypto\Encryption\Entity\AccountDecryptionResult;
use App\Model\Entity\Account;
use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\DecryptionFailure;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use App\Model\Repository\Entity\SubForumRepository;
use App\Model\Repository\Entity\Thread\ThreadRepository;
use DateTimeImmutable;
use Exception;
use Generator;
use ParagonIE\HiddenString\HiddenString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Fixture\DataTypeProvider;
use function is_numeric;
use function is_scalar;
use const PHP_INT_MAX;

class ViewControllerTest extends TestCase
{
    private MockObject&ThreadRepository $threadRepository;
    private MockObject&AccountEncryptor $accountEncryptor;
    private MockObject&LoggerInterface $logger;
    private MockObject&SubForumRepository $subForumRepository;
    private MockObject&ThreadEncryptor $threadEncryptor;
    private ViewController $controller;

    protected function setUp(): void
    {
        $this->threadRepository = $this
            ->getMockBuilder(ThreadRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->accountEncryptor = $this
            ->getMockBuilder(AccountEncryptor::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->subForumRepository = $this
            ->getMockBuilder(SubForumRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->threadEncryptor = $this
            ->getMockBuilder(ThreadEncryptor::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->controller = new ViewController(
            $this->threadRepository,
            $this->accountEncryptor,
            $this->logger,
            $this->subForumRepository,
            $this->threadEncryptor,
        );
    }

    #[Test]
    public function itWillReturnNotFoundIfNoSubForumWithIdFromUri(): void
    {
        $request = new Request();

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn(null)
        ;

        $this->threadRepository
            ->expects(self::never())
            ->method('getPageForSubForum')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame([], JsonHelper::decode($response->getContent()));
    }

    #[Test]
    public function itWillLogAndReturnServerErrorIfDecryptionFailureOnAuthor(): void
    {
        $request = new Request();

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, 25)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willThrowException(DecryptionFailure::because($author, new Exception('oh no')))
        ;

        $this->logger
            ->expects(self::once())
            ->method('critical')
            ->with(self::matchesRegularExpression('/Could not view SubForum;.+oh no/'))
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    #[Test]
    public function itWillLogAndReturnServerErrorIfDecryptionFailureOnThread(): void
    {
        $request = new Request();

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, 25)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('l'),
                    new HiddenString('m'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($thread)
            ->willThrowException(DecryptionFailure::because($thread, new Exception('oh no')))
        ;

        $this->logger
            ->expects(self::once())
            ->method('critical')
            ->with(self::matchesRegularExpression('/Could not view SubForum;.+oh no/'))
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    #[Test]
    public function itWillReturnCorrectSerialisedDataOnSuccess(): void
    {
        $request = new Request();

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread1 = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );
        $thread2 = new Thread(
            'l',
            $subForum,
            $author,
            new EncryptedString('m'),
            new DateTimeImmutable('1991-01-02 03:04:05'),
            true,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, 25)
            ->willReturn([$thread1, $thread2])
        ;

        $this->accountEncryptor
            ->expects(self::exactly(2))
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('n'),
                    new HiddenString('o'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::exactly(2))
            ->method('decrypt')
            ->with(self::logicalOr(self::identicalTo($thread1), self::identicalTo($thread2)))
            ->willReturnCallback(
                function (Thread $arg) use ($thread1, $thread2): HiddenString {
                    if ($thread1 === $arg) {
                        return new HiddenString('p');
                    }

                    if ($thread2 === $arg) {
                        return new HiddenString('q');
                    }

                    self::fail('Invalid argument provided.');
                },
            )
        ;

        $this->logger
            ->expects(self::never())
            ->method('critical')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(
            [
                'id' => 'a',
                'name' => 'b',
                'threads' => [
                    [
                        'id' => 'j',
                        'name' => 'p',
                        'createdAt' => '1990-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => 'd',
                            'username' => 'n',
                        ],
                    ],
                    [
                        'id' => 'l',
                        'name' => 'q',
                        'createdAt' => '1991-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => 'd',
                            'username' => 'n',
                        ],
                    ],
                ],
            ],
            JsonHelper::decode($response->getContent()),
        );
    }

    #[Test]
    #[DataProvider('invalidOrMissingPageProvider')]
    public function itWillProvideADefaultValueForPageIfItIsNotProvidedOrNotNumericOrInvalid(array $queryData): void
    {
        $request = new Request($queryData);

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, 25)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('n'),
                    new HiddenString('o'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($thread)
            ->willReturn(new HiddenString('a'))
        ;

        $this->logger
            ->expects(self::never())
            ->method('critical')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    #[Test]
    #[DataProvider('invalidOrMissingLimitProvider')]
    public function itWillProvideADefaultValueForLimitIfItIsNotProvidedOrNotNumericOrInvalid(
        array $queryData,
        int $expectedLimit,
    ): void {
        $request = new Request($queryData);

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, $expectedLimit)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('n'),
                    new HiddenString('o'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($thread)
            ->willReturn(new HiddenString('a'))
        ;

        $this->logger
            ->expects(self::never())
            ->method('critical')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function invalidOrMissingPageProvider(): Generator
    {
        yield [[]];

        foreach (DataTypeProvider::jsonDataTypes() as $type => $value) {
            if (is_numeric($value) || (! is_scalar($value))) {
                continue;
            }

            yield $type => [['page' => $value]];
        }

        yield [['page' => -1]];
        yield [['page' => -100]];
        yield [['page' => 0]];
    }

    public static function invalidOrMissingLimitProvider(): Generator
    {
        yield [[], 25];

        foreach (DataTypeProvider::jsonDataTypes() as $type => $value) {
            if (is_numeric($value) || (! is_scalar($value))) {
                continue;
            }

            yield $type => [['limit' => $value], 25];
        }

        yield [['limit' => -1], 1];
        yield [['limit' => -100], 1];
        yield [['limit' => 0], 1];
    }

    #[Test]
    #[DataProvider('validPageProvider')]
    public function itWillRespectProvidedPageNumber(string|int|float $page, int $expectedPage): void
    {
        $request = new Request(['page' => $page]);

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, $expectedPage, 25)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('n'),
                    new HiddenString('o'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($thread)
            ->willReturn(new HiddenString('a'))
        ;

        $this->logger
            ->expects(self::never())
            ->method('critical')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function validPageProvider(): array
    {
        return [
            [PHP_INT_MAX, 10000],
            [10000.0001, 10000],
            ['10000.0001', 10000],
            [10001, 10000],
            [4, 4],
            [4.3, 4],
            ['5', 5],
            ['5.4', 5],
            ['5.7', 5],
            [5.5, 5],
        ];
    }

    #[Test]
    #[DataProvider('validLimitProvider')]
    public function itWillRespectProvidedLimit(string|int|float $limit, int $expectedLimit): void
    {
        $request = new Request(['limit' => $limit]);

        $subForum = new SubForum('a', 'b', 'c');
        $author = new Account(
            'd',
            new EncryptedString('e'),
            new HashedString('f'),
            new EncryptedString('g'),
            new HashedString('h'),
            new HashedString('i'),
            [],
        );
        $thread = new Thread(
            'j',
            $subForum,
            $author,
            new EncryptedString('k'),
            new DateTimeImmutable('1990-01-02 03:04:05'),
            false,
        );

        $this->subForumRepository
            ->expects(self::once())
            ->method('findById')
            ->with('3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d')
            ->willReturn($subForum)
        ;

        $this->threadRepository
            ->expects(self::once())
            ->method('getPageForSubForum')
            ->with($subForum, 1, $expectedLimit)
            ->willReturn([$thread])
        ;

        $this->accountEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($author)
            ->willReturn(
                new AccountDecryptionResult(
                    new HiddenString('n'),
                    new HiddenString('o'),
                ),
            )
        ;

        $this->threadEncryptor
            ->expects(self::once())
            ->method('decrypt')
            ->with($thread)
            ->willReturn(new HiddenString('a'))
        ;

        $this->logger
            ->expects(self::never())
            ->method('critical')
        ;

        $response = ($this->controller)($request, '3360438b-82b4-4e9c-b69f-b3ed9f2f5e9d');

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function validLimitProvider(): array
    {
        return [
            [PHP_INT_MAX, 50],
            [10000.0001, 50],
            ['10000.0001', 50],
            [10001, 50],
            [50, 50],
            ['50', 50],
            ['51', 50],
            [51, 50],
            [50.01, 50],
            [50.001, 50],
            ['50.001', 50],
            [4, 4],
            [4.3, 4],
            ['5', 5],
            ['5.4', 5],
            ['5.7', 5],
            [5.5, 5],
        ];
    }
}
