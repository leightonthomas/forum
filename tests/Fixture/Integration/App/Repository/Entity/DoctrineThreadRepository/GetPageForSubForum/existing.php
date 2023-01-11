<?php

declare(strict_types=1);

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Crypto\Encryption\Entity\Thread\ThreadEncryptor;
use App\Model\Entity\Account;
use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use ParagonIE\HiddenString\HiddenString;
use Psr\Container\ContainerInterface;

return static function (ContainerInterface $container) {
    $hasher = $container->get('crypto.hashing.entity.account.password_hashing_method');
    $hashedPassword = $hasher->hash(new HiddenString('apassword'));

    $accountEncryptor = $container->get(AccountEncryptor::class);
    $threadEncryptor = $container->get(ThreadEncryptor::class);

    $bobEncrypted = $accountEncryptor->encrypt(
        '9afbee8b-303f-4582-a260-b6552810bd33',
        new HiddenString('bob'),
        new HiddenString('Bob@ExAmPlE.com'),
    );

    $bob = new Account(
        '9afbee8b-303f-4582-a260-b6552810bd33',
        $bobEncrypted->username,
        $bobEncrypted->usernameBlindIndex,
        $bobEncrypted->emailAddress,
        $bobEncrypted->emailAddressFullBlindIndex,
        $hashedPassword,
        ['ROLE_USER'],
    );

    $subForum = new SubForum('7c063d24-bbc8-4636-aeb7-9de334402102', 'A', 'a');
    $subForumB = new SubForum('349b66e7-10af-46eb-b71f-ef6b52391b0a', 'B', 'b');
    $subForumC = new SubForum('23016880-632d-435d-a2be-f15b5bb88893', 'C', 'c');

    $threadConfig = [
        ['id' => '6928ba86-61b8-4b18-a8f0-cb770c7c9f8f', 'forum' => $subForum],
        ['id' => '0acdde16-06df-495b-9a86-62601c18001a', 'forum' => $subForum],
        ['id' => '32b3cd28-814c-444e-a617-30c76d847f5d', 'forum' => $subForum],
        ['id' => '9bf28fe5-e148-4d28-a214-2f19e927d6e1', 'forum' => $subForum],
        ['id' => 'cbc8c708-e981-46a2-ab90-19dd42d00957', 'forum' => $subForum],
        ['id' => '65ebcfa4-b2b0-4ae9-96ea-12990b2aeb02', 'forum' => $subForum],
        ['id' => 'ca2cc94e-ca87-4cd7-9e77-9c68e9dc871a', 'forum' => $subForum],
        ['id' => '2eb614fb-80e0-4563-8756-49a88e89dffa', 'forum' => $subForum],
        ['id' => '2016f913-4780-49e2-8b3b-081340f4daf2', 'forum' => $subForum],
        ['id' => 'd654c673-6441-409d-8dd0-1f3cda801c98', 'forum' => $subForum],
        ['id' => '9e84eab3-34b1-454c-b8cf-c0f10eced8f0', 'forum' => $subForum],
        ['id' => '1508dff4-4200-41f1-96c0-796f11284bcb', 'forum' => $subForum],
        ['id' => '73a6034d-50a8-496b-a116-a2f4398345e6', 'forum' => $subForum],
        ['id' => 'ac3c5a29-54b7-40f3-a6bb-3f1170953ff1', 'forum' => $subForum],
        ['id' => '50ed1cf4-2584-42b2-9d69-077601d3f967', 'forum' => $subForum],
        ['id' => 'ee0de738-79b5-4822-b545-64211ba8df62', 'forum' => $subForum],
        ['id' => 'f27225b9-3733-4520-982a-ee5cbffb2d0d', 'forum' => $subForum],
        ['id' => 'f21389a3-f16a-4837-9034-6cebe4f3787e', 'forum' => $subForum],
        ['id' => '7f4c10d8-083d-48ea-8abc-4175260e634a', 'forum' => $subForum],
        ['id' => '1095be90-9024-4fc6-9b02-726f34e4241e', 'forum' => $subForum],
        ['id' => 'db52d1bd-1f6f-4d8b-8716-af019be2e5a4', 'forum' => $subForum],
        ['id' => 'b83672bc-5baa-44ef-9090-b7e9a3d87083', 'forum' => $subForum],
        ['id' => '9ab66ab9-965a-4d7d-896d-37b7ff86399c', 'forum' => $subForum],
        ['id' => '9552aaaf-f1af-430d-a4e8-755802af125c', 'forum' => $subForum],
        ['id' => '616c5fdf-2224-4fb1-a761-8f3198eec781', 'forum' => $subForum],
        ['id' => 'cf1818a5-28ed-4155-9ece-05b16ac636d5', 'forum' => $subForum],
        ['id' => 'a1307de3-593a-4734-94bc-ee9ebc38a848', 'forum' => $subForum],
        ['id' => '72d1ce8e-b121-4a8d-a07d-986befcd369c', 'forum' => $subForum],
        ['id' => 'bd33719d-9a73-4b31-a79d-5f85465ec259', 'forum' => $subForum],
        ['id' => 'e487a5bf-3a71-4e81-b710-e4d4101ddd0e', 'forum' => $subForum],
        ['id' => 'dcb36fce-c73b-4a99-86cf-cff8311e11c0', 'forum' => $subForum],
        ['id' => '77ce1342-3a3b-4a04-b115-8c2c786dae69', 'forum' => $subForum],
        ['id' => '886bf5fe-632d-4e66-99c8-fb88a9f6452f', 'forum' => $subForum],
        ['id' => '645238e5-f340-467e-9099-2afc4543bd49', 'forum' => $subForum],
        ['id' => '107c9dfc-6ab7-4a00-b885-737bc7902862', 'forum' => $subForum],
        ['id' => 'e5f7cb64-2b1a-444d-a68a-003b68b67368', 'forum' => $subForum],
        ['id' => '3db21462-22f9-468d-b1f7-4e08ca95cd56', 'forum' => $subForum],
        ['id' => '14bd6cdc-b8ba-4b21-a5a7-8e8e03b2ac86', 'forum' => $subForum],
        ['id' => '71eca4a4-2cf9-40a7-b22f-3c7d1f7c5789', 'forum' => $subForum],
        ['id' => '3c60a1ea-4a9b-44b7-8fed-cbf051eb202f', 'forum' => $subForum],
        ['id' => '21764281-e7f6-450d-a9c3-87e2c279b0b5', 'forum' => $subForum],
        ['id' => 'bfee59ae-bd8b-4245-a85b-e84be5c040a6', 'forum' => $subForum],
        ['id' => '31e8bfa0-aad5-4a9f-a876-856ca2cf576e', 'forum' => $subForum],
        ['id' => 'fcaf8282-b409-4b02-b2a7-5f561a20c3e5', 'forum' => $subForum],
        ['id' => 'c2f0dc38-befb-4679-9d78-13a56ca32555', 'forum' => $subForum],
        ['id' => '091e8064-fb02-4c6f-9b85-6892cfe6ab61', 'forum' => $subForum],
        ['id' => 'dc2e136d-c44d-4770-ae5e-1d298b51a599', 'forum' => $subForum],
        ['id' => 'c9516e63-f9e3-4d98-81c4-b87a0dd30d70', 'forum' => $subForum],
        ['id' => '9d8c58c6-4cd5-44a5-b272-4cf3101131d0', 'forum' => $subForum],
        ['id' => '36c49d42-e1f2-4029-9909-61c309c09ee0', 'forum' => $subForum],
        ['id' => '07b6b15f-90a5-4427-888b-dcd4877e387e', 'forum' => $subForum],
        ['id' => '52f998fd-d789-40b2-82fe-829f77c2bf98', 'forum' => $subForum],
        ['id' => '9af880fd-f289-4350-bca4-3e37ce39e524', 'forum' => $subForum],
        ['id' => 'd01d6fd4-9f11-47a8-86d1-420b0967725d', 'forum' => $subForum],
        ['id' => 'ced52bfc-44c5-4498-bb67-ecba462c793c', 'forum' => $subForum],
        ['id' => '95056c6a-89dd-4afe-8bce-3fe00c1d10c5', 'forum' => $subForum],
        ['id' => '731c9725-5658-4ee7-b54c-8216a1f28f9d', 'forum' => $subForum],
        ['id' => '6203aa8f-9854-40e9-8511-8424b5c2e137', 'forum' => $subForum],
        ['id' => 'afd86142-89f5-4f7d-bf5b-040c23af1c88', 'forum' => $subForum],
        ['id' => '70481beb-18a1-4f4e-9008-4f94f275ebbe', 'forum' => $subForum],
        ['id' => '91ed0ee0-dce4-4335-a1de-7ef1ab2a73ad', 'forum' => $subForum],
        ['id' => 'a97ffbf1-f77e-4635-b96f-92e340f95626', 'forum' => $subForum],
        ['id' => 'dbda623e-73f7-4ccf-aac9-2f9a4a07a4de', 'forum' => $subForum],
        ['id' => 'be75bc20-7b26-4209-a1df-7b1f5e3ef5d2', 'forum' => $subForum],
        ['id' => 'd2f52430-5a85-4a68-8a34-dda5f4773e33', 'forum' => $subForum],
        ['id' => '3f6a3bab-6a27-4f82-aba3-b5ddb8b36bba', 'forum' => $subForum],
        ['id' => 'c1994a34-6db1-4093-b8c0-23369bc85c11', 'forum' => $subForum],
        ['id' => 'f3de825d-57dc-463e-a74a-2e80a76d5b6e', 'forum' => $subForum],
        ['id' => 'd33f9d1e-c3e1-46c7-b1ba-f3e2f3e65efb', 'forum' => $subForum],
        ['id' => 'a857deff-c084-4171-91bc-fee554a7f9dd', 'forum' => $subForum],
        ['id' => '00828420-c6e8-41ca-a14d-79cef444f063', 'forum' => $subForum],
        ['id' => 'd728df35-eb81-4ae1-ac29-90eb2fdb7510', 'forum' => $subForum],
        ['id' => 'badc4217-ac71-43d5-89a0-3b653b596c91', 'forum' => $subForum],
        ['id' => '059ab249-1ddd-4787-8eb9-338a877be719', 'forum' => $subForum],
        ['id' => '7f064aaa-1af8-4990-8a32-69e8cc338861', 'forum' => $subForum],
        ['id' => '9e538de0-030b-4c01-a079-7b6e1bf67820', 'forum' => $subForum],
        ['id' => '83d910c8-6ab0-4bb0-938d-8a2e165185b1', 'forum' => $subForum],
        ['id' => '97908729-05e2-46fd-ad61-d45682981a5e', 'forum' => $subForum],
        ['id' => '45bfc1bb-f29d-4a5b-8b7c-a9e3b38b8bd2', 'forum' => $subForum],
        ['id' => '833a06d4-e06d-42ff-be2e-0f5ec65fb1e1', 'forum' => $subForum],
        ['id' => '3340785d-4831-4544-824b-d5ccbbc43246', 'forum' => $subForum],
        ['id' => '332d0bfe-062c-47dc-997e-228f74d64f6e', 'forum' => $subForum],
        ['id' => '32cd94f7-8c1f-4e4c-a62e-4c155609aef2', 'forum' => $subForum],
        ['id' => 'b5c6ce54-cb6b-4019-bfc8-9a321b111dcc', 'forum' => $subForum],
        ['id' => '1204e77c-1500-4a4c-a582-4943776898a7', 'forum' => $subForum],
        ['id' => '7429d548-e733-4fe7-a3d1-95341841d84b', 'forum' => $subForum],
        ['id' => '5b6233af-c4ed-4ed2-8d71-13035e2e9649', 'forum' => $subForum],
        ['id' => '0774c518-3aa9-46e7-a6df-0a1ee4c68b51', 'forum' => $subForum],
        ['id' => '25f6de3f-7aad-428a-ad8a-578e758d0b71', 'forum' => $subForum],
        ['id' => '6f5d9deb-55ab-49a4-8a15-d281b8b81597', 'forum' => $subForum],
        ['id' => '7874763e-54c9-4f4d-af75-69c4796d3b78', 'forum' => $subForum],
        ['id' => '135c6883-f81a-4f6d-b4f4-2a8677d9aada', 'forum' => $subForum],
        ['id' => '8604c029-6606-4dda-b443-b7a905cf8488', 'forum' => $subForum],
        ['id' => 'fb3d5c58-120c-4420-b282-283673fd0f72', 'forum' => $subForum],
        ['id' => '11a755a1-05ca-4c44-a9e7-530cd123f244', 'forum' => $subForum],
        ['id' => 'df5f2579-eae9-4e99-86df-9eccf8d5ae0c', 'forum' => $subForum],
        ['id' => '2e4b6068-ec12-44ea-9abe-0db10cc2f1e4', 'forum' => $subForum],
        ['id' => '1a57cf8b-65e3-496e-9311-19a3d02dc6a4', 'forum' => $subForum],
        ['id' => '48c87aa0-4fe3-4faa-a11c-caa0587c5216', 'forum' => $subForum],
        ['id' => '40c7c27b-b0e2-424a-973b-b12f762b4282', 'forum' => $subForum],
        ['id' => '9d52a5b3-e408-43e1-b1af-62e62983b2ba', 'forum' => $subForum],
        ['id' => '7ddcf860-922a-4689-a3a0-e6e1034eb95b', 'forum' => $subForum],
        ['id' => '55105bf5-20d9-4912-bc93-eb981991cc2c', 'forum' => $subForumB],
        ['id' => 'c2c45c3b-0bc1-403a-99f4-971654dc286d', 'forum' => $subForumB],
        ['id' => '5d5549d1-f784-48dc-9dca-58a337fe36bb', 'forum' => $subForumB],
        ['id' => 'c73f2d31-ab19-4ff2-ba17-75ae5a20d56c', 'forum' => $subForumB],
        ['id' => '6349b0ef-ee02-41fe-ac70-176c45bb182e', 'forum' => $subForumB],
        ['id' => '6de91f6e-95b9-4db9-afe1-0530ac22e7d3', 'forum' => $subForumB],
        ['id' => '748c6b4c-fa78-4101-b581-dd02a196a872', 'forum' => $subForumB],
    ];

    $createThread = function (
        string $id,
        int $index,
        SubForum $forum,
    ) use ($bob, $threadEncryptor): Thread {
        $encrypted = $threadEncryptor->encrypt($id, new HiddenString("thread $index"));
        $date = new DateTimeImmutable('1990-01-02 03:04:05');
        $date = $date->add(new DateInterval("P{$index}Y"));

        return new Thread(
            $id,
            $forum,
            $bob,
            $encrypted,
            $date,
            false,
        );
    };

    $threads = [];
    foreach ($threadConfig as $index => $config) {
        $threads[] = $createThread($config['id'], $index, $config['forum']);
    }

    return [
        $bob,
        $subForum,
        $subForumB,
        $subForumC,
        ...$threads,
    ];
};
