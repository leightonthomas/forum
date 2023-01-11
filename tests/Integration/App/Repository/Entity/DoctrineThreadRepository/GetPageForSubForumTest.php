<?php

declare(strict_types=1);

namespace Tests\Integration\App\Repository\Entity\DoctrineThreadRepository;

use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use App\Model\Repository\Entity\SubForumRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;
use function array_map;

#[FixtureDirectory('Integration/App/Repository/Entity/DoctrineThreadRepository/GetPageForSubForum')]
class GetPageForSubForumTest extends DoctrineThreadRepositoryTestCase
{
    #[Test]
    #[Fixture('existing.php')]
    public function itWillReturnEmptyIfNoResults(): void
    {
        $subForum = $this->getForum('23016880-632d-435d-a2be-f15b5bb88893');

        $results = $this->repository->getPageForSubForum($subForum, 1, 2);
        self::assertEmpty($results);

        $results = $this->repository->getPageForSubForum($subForum, 2, 2);
        self::assertEmpty($results);
    }

    #[Test]
    #[Fixture('existing.php')]
    #[DataProvider('paginationProvider')]
    public function itWillReturnAllThreadsOrderedByCreationDate(
        int $page,
        int $limit,
        array $expected,
    ): void {
        $subForum = $this->getForum('7c063d24-bbc8-4636-aeb7-9de334402102');

        $results = $this->repository->getPageForSubForum($subForum, $page, $limit);
        $results = array_map(fn(Thread $t) => $t->getId(), $results);

        self::assertSame($expected, $results);
    }

    public static function paginationProvider(): array
    {
        return [
            [
                'page' => 1,
                'limit' => 5,
                'expected' => [
                    '7ddcf860-922a-4689-a3a0-e6e1034eb95b',
                    '9d52a5b3-e408-43e1-b1af-62e62983b2ba',
                    '40c7c27b-b0e2-424a-973b-b12f762b4282',
                    '48c87aa0-4fe3-4faa-a11c-caa0587c5216',
                    '1a57cf8b-65e3-496e-9311-19a3d02dc6a4',
                ],
            ],
            [
                'page' => 2,
                'limit' => 5,
                'expected' => [
                    '2e4b6068-ec12-44ea-9abe-0db10cc2f1e4',
                    'df5f2579-eae9-4e99-86df-9eccf8d5ae0c',
                    '11a755a1-05ca-4c44-a9e7-530cd123f244',
                    'fb3d5c58-120c-4420-b282-283673fd0f72',
                    '8604c029-6606-4dda-b443-b7a905cf8488',
                ],
            ],
            [
                'page' => 21,
                'limit' => 5,
                'expected' => [
                    '0acdde16-06df-495b-9a86-62601c18001a',
                    '6928ba86-61b8-4b18-a8f0-cb770c7c9f8f',
                ],
            ],
            'minimum page' => [
                'page' => -1,
                'limit' => 5,
                'expected' => [
                    '7ddcf860-922a-4689-a3a0-e6e1034eb95b',
                    '9d52a5b3-e408-43e1-b1af-62e62983b2ba',
                    '40c7c27b-b0e2-424a-973b-b12f762b4282',
                    '48c87aa0-4fe3-4faa-a11c-caa0587c5216',
                    '1a57cf8b-65e3-496e-9311-19a3d02dc6a4',
                ],
            ],
            'minimum limit' => [
                'page' => 1,
                'limit' => -1,
                'expected' => [
                    '7ddcf860-922a-4689-a3a0-e6e1034eb95b',
                ],
            ],
            'maximum limit' => [
                'page' => 1,
                'limit' => 9999999999,
                'expected' => [
                    '7ddcf860-922a-4689-a3a0-e6e1034eb95b',
                    '9d52a5b3-e408-43e1-b1af-62e62983b2ba',
                    '40c7c27b-b0e2-424a-973b-b12f762b4282',
                    '48c87aa0-4fe3-4faa-a11c-caa0587c5216',
                    '1a57cf8b-65e3-496e-9311-19a3d02dc6a4',
                    '2e4b6068-ec12-44ea-9abe-0db10cc2f1e4',
                    'df5f2579-eae9-4e99-86df-9eccf8d5ae0c',
                    '11a755a1-05ca-4c44-a9e7-530cd123f244',
                    'fb3d5c58-120c-4420-b282-283673fd0f72',
                    '8604c029-6606-4dda-b443-b7a905cf8488',
                    '135c6883-f81a-4f6d-b4f4-2a8677d9aada',
                    '7874763e-54c9-4f4d-af75-69c4796d3b78',
                    '6f5d9deb-55ab-49a4-8a15-d281b8b81597',
                    '25f6de3f-7aad-428a-ad8a-578e758d0b71',
                    '0774c518-3aa9-46e7-a6df-0a1ee4c68b51',
                    '5b6233af-c4ed-4ed2-8d71-13035e2e9649',
                    '7429d548-e733-4fe7-a3d1-95341841d84b',
                    '1204e77c-1500-4a4c-a582-4943776898a7',
                    'b5c6ce54-cb6b-4019-bfc8-9a321b111dcc',
                    '32cd94f7-8c1f-4e4c-a62e-4c155609aef2',
                    '332d0bfe-062c-47dc-997e-228f74d64f6e',
                    '3340785d-4831-4544-824b-d5ccbbc43246',
                    '833a06d4-e06d-42ff-be2e-0f5ec65fb1e1',
                    '45bfc1bb-f29d-4a5b-8b7c-a9e3b38b8bd2',
                    '97908729-05e2-46fd-ad61-d45682981a5e',
                    '83d910c8-6ab0-4bb0-938d-8a2e165185b1',
                    '9e538de0-030b-4c01-a079-7b6e1bf67820',
                    '7f064aaa-1af8-4990-8a32-69e8cc338861',
                    '059ab249-1ddd-4787-8eb9-338a877be719',
                    'badc4217-ac71-43d5-89a0-3b653b596c91',
                    'd728df35-eb81-4ae1-ac29-90eb2fdb7510',
                    '00828420-c6e8-41ca-a14d-79cef444f063',
                    'a857deff-c084-4171-91bc-fee554a7f9dd',
                    'd33f9d1e-c3e1-46c7-b1ba-f3e2f3e65efb',
                    'f3de825d-57dc-463e-a74a-2e80a76d5b6e',
                    'c1994a34-6db1-4093-b8c0-23369bc85c11',
                    '3f6a3bab-6a27-4f82-aba3-b5ddb8b36bba',
                    'd2f52430-5a85-4a68-8a34-dda5f4773e33',
                    'be75bc20-7b26-4209-a1df-7b1f5e3ef5d2',
                    'dbda623e-73f7-4ccf-aac9-2f9a4a07a4de',
                    'a97ffbf1-f77e-4635-b96f-92e340f95626',
                    '91ed0ee0-dce4-4335-a1de-7ef1ab2a73ad',
                    '70481beb-18a1-4f4e-9008-4f94f275ebbe',
                    'afd86142-89f5-4f7d-bf5b-040c23af1c88',
                    '6203aa8f-9854-40e9-8511-8424b5c2e137',
                    '731c9725-5658-4ee7-b54c-8216a1f28f9d',
                    '95056c6a-89dd-4afe-8bce-3fe00c1d10c5',
                    'ced52bfc-44c5-4498-bb67-ecba462c793c',
                    'd01d6fd4-9f11-47a8-86d1-420b0967725d',
                    '9af880fd-f289-4350-bca4-3e37ce39e524',
                    '52f998fd-d789-40b2-82fe-829f77c2bf98',
                    '07b6b15f-90a5-4427-888b-dcd4877e387e',
                    '36c49d42-e1f2-4029-9909-61c309c09ee0',
                    '9d8c58c6-4cd5-44a5-b272-4cf3101131d0',
                    'c9516e63-f9e3-4d98-81c4-b87a0dd30d70',
                    'dc2e136d-c44d-4770-ae5e-1d298b51a599',
                    '091e8064-fb02-4c6f-9b85-6892cfe6ab61',
                    'c2f0dc38-befb-4679-9d78-13a56ca32555',
                    'fcaf8282-b409-4b02-b2a7-5f561a20c3e5',
                    '31e8bfa0-aad5-4a9f-a876-856ca2cf576e',
                    'bfee59ae-bd8b-4245-a85b-e84be5c040a6',
                    '21764281-e7f6-450d-a9c3-87e2c279b0b5',
                    '3c60a1ea-4a9b-44b7-8fed-cbf051eb202f',
                    '71eca4a4-2cf9-40a7-b22f-3c7d1f7c5789',
                    '14bd6cdc-b8ba-4b21-a5a7-8e8e03b2ac86',
                    '3db21462-22f9-468d-b1f7-4e08ca95cd56',
                    'e5f7cb64-2b1a-444d-a68a-003b68b67368',
                    '107c9dfc-6ab7-4a00-b885-737bc7902862',
                    '645238e5-f340-467e-9099-2afc4543bd49',
                    '886bf5fe-632d-4e66-99c8-fb88a9f6452f',
                    '77ce1342-3a3b-4a04-b115-8c2c786dae69',
                    'dcb36fce-c73b-4a99-86cf-cff8311e11c0',
                    'e487a5bf-3a71-4e81-b710-e4d4101ddd0e',
                    'bd33719d-9a73-4b31-a79d-5f85465ec259',
                    '72d1ce8e-b121-4a8d-a07d-986befcd369c',
                    'a1307de3-593a-4734-94bc-ee9ebc38a848',
                    'cf1818a5-28ed-4155-9ece-05b16ac636d5',
                    '616c5fdf-2224-4fb1-a761-8f3198eec781',
                    '9552aaaf-f1af-430d-a4e8-755802af125c',
                    '9ab66ab9-965a-4d7d-896d-37b7ff86399c',
                    'b83672bc-5baa-44ef-9090-b7e9a3d87083',
                    'db52d1bd-1f6f-4d8b-8716-af019be2e5a4',
                    '1095be90-9024-4fc6-9b02-726f34e4241e',
                    '7f4c10d8-083d-48ea-8abc-4175260e634a',
                    'f21389a3-f16a-4837-9034-6cebe4f3787e',
                    'f27225b9-3733-4520-982a-ee5cbffb2d0d',
                    'ee0de738-79b5-4822-b545-64211ba8df62',
                    '50ed1cf4-2584-42b2-9d69-077601d3f967',
                    'ac3c5a29-54b7-40f3-a6bb-3f1170953ff1',
                    '73a6034d-50a8-496b-a116-a2f4398345e6',
                    '1508dff4-4200-41f1-96c0-796f11284bcb',
                    '9e84eab3-34b1-454c-b8cf-c0f10eced8f0',
                    'd654c673-6441-409d-8dd0-1f3cda801c98',
                    '2016f913-4780-49e2-8b3b-081340f4daf2',
                    '2eb614fb-80e0-4563-8756-49a88e89dffa',
                    'ca2cc94e-ca87-4cd7-9e77-9c68e9dc871a',
                    '65ebcfa4-b2b0-4ae9-96ea-12990b2aeb02',
                    'cbc8c708-e981-46a2-ab90-19dd42d00957',
                    '9bf28fe5-e148-4d28-a214-2f19e927d6e1',
                    '32b3cd28-814c-444e-a617-30c76d847f5d',
                ],
            ],
        ];
    }

    private function getForum(string $id): SubForum
    {
        $repository = static::getContainer()->get(SubForumRepository::class);

        $forum = $repository->findById($id);

        self::assertInstanceOf(SubForum::class, $forum, "Could not find SubForum [$id]");

        return $forum;
    }
}
