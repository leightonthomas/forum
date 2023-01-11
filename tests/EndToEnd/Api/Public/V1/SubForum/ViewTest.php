<?php

declare(strict_types=1);

namespace Tests\EndToEnd\Api\Public\V1\SubForum;

use App\Helper\JsonHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\EndToEnd\Api\ApiTestCase;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;

#[FixtureDirectory('EndToEnd/Api/Public/V1/SubForum/View')]
class ViewTest extends ApiTestCase
{
    #[Test]
    #[Fixture('existing.php')]
    public function itWillReturnNotFoundResponseIfSubForumNotRecognised(): void
    {
        $response = $this->get('/public/v1/subforum/f13f2ebf-78d9-489a-9ad9-858b4a1aea70')['response'];

        self::assertJsonResponse(Response::HTTP_NOT_FOUND, [], $response);
    }

    #[Test]
    #[Fixture('existing.php')]
    public function itWillReturnSubForumIfFound(): void
    {
        $response = $this->get('/public/v1/subforum/6a2cf613-358b-45ff-b8ff-1768d5382f20')['response'];

        self::assertJsonResponse(
            Response::HTTP_OK,
            [
                'id' => '6a2cf613-358b-45ff-b8ff-1768d5382f20',
                'name' => 'Forum A',
                'threads' => [
                    [
                        'id' => '230940f4-613b-455c-9ca7-55a9f6402424',
                        'name' => 'thread 49',
                        'createdAt' => '2039-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'c8489a9d-0f8d-4811-a677-d27e18b1d764',
                        'name' => 'thread 48',
                        'createdAt' => '2038-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '195e0e5f-e53b-4e2f-aea1-617a1ac5d636',
                        'name' => 'thread 47',
                        'createdAt' => '2037-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '63ec97d4-e6b0-4898-964f-2596228bd062',
                        'name' => 'thread 46',
                        'createdAt' => '2036-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'e6994eb7-9a4d-4030-a325-8dad1bcb3bb4',
                        'name' => 'thread 45',
                        'createdAt' => '2035-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '4e55e6b9-e047-41a1-8d53-ed3b2ba9881c',
                        'name' => 'thread 44',
                        'createdAt' => '2034-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'b1010583-d0a7-453e-8f6d-a295e3d9e5b9',
                        'name' => 'thread 43',
                        'createdAt' => '2033-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '60079302-b87c-4e4a-b0d9-59bef0d4c91f',
                        'name' => 'thread 42',
                        'createdAt' => '2032-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '46600e17-5cd2-4e7d-b8cf-2949457deaab',
                        'name' => 'thread 41',
                        'createdAt' => '2031-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '52deb80c-143f-48ab-81f9-f8d39fb81cea',
                        'name' => 'thread 40',
                        'createdAt' => '2030-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '937ef2c2-6bd3-4045-8bf7-d0f19820d2e2',
                        'name' => 'thread 39',
                        'createdAt' => '2029-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'f2cec334-00a0-4a66-a133-013ffa70caf6',
                        'name' => 'thread 38',
                        'createdAt' => '2028-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '2621f904-d4e0-44fe-9abb-529384e6351a',
                        'name' => 'thread 37',
                        'createdAt' => '2027-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '3dadc8cf-eb9d-444b-b12d-7d06d6bcd450',
                        'name' => 'thread 36',
                        'createdAt' => '2026-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '6844879a-f3e8-4628-889c-41af573c73ca',
                        'name' => 'thread 35',
                        'createdAt' => '2025-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '3189e3ba-9c50-406c-a4ea-66da5be2902a',
                        'name' => 'thread 34',
                        'createdAt' => '2024-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '95fa5e16-db50-4c70-b5eb-0a299a7af1d8',
                        'name' => 'thread 33',
                        'createdAt' => '2023-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '21d3bdc1-17d7-45a6-acc0-af5ae096be90',
                        'name' => 'thread 32',
                        'createdAt' => '2022-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '91903b51-8007-4226-a622-956dec909a80',
                        'name' => 'thread 31',
                        'createdAt' => '2021-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '3d572d15-087e-4ea0-9b40-f79241d2bcd5',
                        'name' => 'thread 30',
                        'createdAt' => '2020-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '782004ba-c9e3-41b0-98c8-184e8596fea6',
                        'name' => 'thread 29',
                        'createdAt' => '2019-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'a1d6aaac-2288-4f3a-85cd-f1ed4be9b3db',
                        'name' => 'thread 28',
                        'createdAt' => '2018-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '2ff35c57-46b3-4031-b144-9bbbfe00207d',
                        'name' => 'thread 27',
                        'createdAt' => '2017-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => 'e285adf3-0a55-4e47-a3b3-9d5a005aaca5',
                        'name' => 'thread 26',
                        'createdAt' => '2016-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ],
                    [
                        'id' => '3ed8d7bc-3711-47ba-bb46-1ce3b6be3385',
                        'name' => 'thread 25',
                        'createdAt' => '2015-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob'
                        ]
                    ]
                ]
            ],
            $response,
        );
    }

    #[Test]
    #[Fixture('existing.php')]
    public function itWillLimitNumberOfThreadsBasedOnQueryParameter(): void
    {
        $response = $this->get('/public/v1/subforum/6a2cf613-358b-45ff-b8ff-1768d5382f20', ['limit' => 2])['response'];

        self::assertJsonResponse(
            Response::HTTP_OK,
            [
                'id' => '6a2cf613-358b-45ff-b8ff-1768d5382f20',
                'name' => 'Forum A',
                'threads' => [
                    [
                        'id' => '230940f4-613b-455c-9ca7-55a9f6402424',
                        'name' => 'thread 49',
                        'createdAt' => '2039-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob',
                        ],
                    ],
                    [
                        'id' => 'c8489a9d-0f8d-4811-a677-d27e18b1d764',
                        'name' => 'thread 48',
                        'createdAt' => '2038-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob',
                        ],
                    ],
                ],
            ],
            $response,
        );
    }

    #[Test]
    #[Fixture('existing.php')]
    public function itWillHaveAnUpperLimitOnTheThreadLimit(): void
    {
        $response = $this->get('/public/v1/subforum/6a2cf613-358b-45ff-b8ff-1768d5382f20', ['limit' => 99])['response'];

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertCount(50, JsonHelper::decode($response->getContent())['threads']);
    }

    #[Test]
    #[Fixture('existing.php')]
    #[DataProvider('lowerLimitProvider')]
    public function itWillHaveALowerLimitOnTheThreadLimit(int $amount): void
    {
        $response = $this->get(
            '/public/v1/subforum/6a2cf613-358b-45ff-b8ff-1768d5382f20',
            ['limit' => $amount],
        )['response'];

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertCount(1, JsonHelper::decode($response->getContent())['threads']);
    }

    public static function lowerLimitProvider(): array
    {
        return [
            [-5],
            [0],
        ];
    }

    #[Test]
    #[Fixture('existing.php')]
    public function itWillPaginateResults(): void
    {
        $response = $this->get(
            '/public/v1/subforum/6a2cf613-358b-45ff-b8ff-1768d5382f20',
            ['limit' => 1, 'page' => 3],
        )['response'];

        self::assertJsonResponse(
            Response::HTTP_OK,
            [
                'id' => '6a2cf613-358b-45ff-b8ff-1768d5382f20',
                'name' => 'Forum A',
                'threads' => [
                    [
                        'id' => '195e0e5f-e53b-4e2f-aea1-617a1ac5d636',
                        'name' => 'thread 47',
                        'createdAt' => '2037-01-02T03:04:05+00:00',
                        'author' => [
                            'id' => '9afbee8b-303f-4582-a260-b6552810bd33',
                            'username' => 'bob',
                        ],
                    ],
                ],
            ],
            $response,
        );
    }
}
