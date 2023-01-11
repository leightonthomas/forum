<?php

declare(strict_types=1);

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Crypto\Encryption\Entity\Thread\ThreadEncryptor;
use App\Model\Entity\Account;
use App\Model\Entity\SubForum;
use App\Model\Entity\Thread\Thread;
use ParagonIE\HiddenString\HiddenString;
use Psr\Container\ContainerInterface;

return static function (ContainerInterface $container): array {
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

    $subForum = new SubForum('6a2cf613-358b-45ff-b8ff-1768d5382f20', 'Forum A', 'forum-a');
    $subForum2 = new SubForum('17ee524b-c919-4831-af8e-6664ea946cd1', 'Forum B', 'forum-b');

    $threadConfig = [
        ['id' => '7433e3e6-b8d5-473e-b7b2-85344789d11f', 'forum' => $subForum, 'pinned' => false],
        ['id' => '75fb9795-46cc-4701-95bb-91434adfeac5', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'a418c001-e528-4e2a-b317-231266cfb88e', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'ac200a70-ec44-460f-be87-53449768c377', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'b3684067-4721-46e7-a607-c8ab494a2f1f', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'ca4c6125-4530-4b6a-a001-ef0b0b21ade7', 'forum' => $subForum, 'pinned' => false],
        ['id' => '30f82e01-fd92-44b0-b732-68cdf2ec7010', 'forum' => $subForum, 'pinned' => false],
        ['id' => '091fac4b-a7a9-4b18-b356-ded4237269c3', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'df682654-fe25-42ea-99af-a93292df1922', 'forum' => $subForum, 'pinned' => false],
        ['id' => '470fbdda-b2d9-42cf-bb2e-8b218e7e0684', 'forum' => $subForum, 'pinned' => true],
        ['id' => 'bd938875-b931-4265-9a66-81ee00ab3377', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'c801ef93-df4f-4540-9488-8348960f68a1', 'forum' => $subForum, 'pinned' => false],
        ['id' => '6b28256c-e81c-423c-859d-17c5cd856b7c', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'c9e68ff8-9926-446a-b56c-3e34418e6ba9', 'forum' => $subForum, 'pinned' => false],
        ['id' => '2841bf03-ba81-47aa-b16c-f9cd1d9be76a', 'forum' => $subForum, 'pinned' => false],
        ['id' => '8c258980-559a-4b8e-a306-417cadb26a9d', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'b64023af-32f1-4511-b9ca-fa79ab503ff6', 'forum' => $subForum, 'pinned' => false],
        ['id' => '4e52c62a-848a-4b83-9d51-1b96aaff9ca6', 'forum' => $subForum, 'pinned' => false],
        ['id' => '7bebf83f-7f73-4698-880d-4fef378a3fc8', 'forum' => $subForum, 'pinned' => false],
        ['id' => '87c2021e-3635-4a96-a242-3bb05f2250ee', 'forum' => $subForum, 'pinned' => false],
        ['id' => '2748b754-59d8-4dec-82d3-b6160a1a00b7', 'forum' => $subForum, 'pinned' => false],
        ['id' => '83752f17-8bfa-46e2-ac7d-fdd8995ee919', 'forum' => $subForum, 'pinned' => false],
        ['id' => '71ad00d4-e4c0-45d5-9105-f8a87343329e', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'd27735aa-fb86-4595-b324-4dab70d8f211', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'fd193ed0-d491-4292-afcf-f42779ae37ac', 'forum' => $subForum, 'pinned' => false],
        ['id' => '3ed8d7bc-3711-47ba-bb46-1ce3b6be3385', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'e285adf3-0a55-4e47-a3b3-9d5a005aaca5', 'forum' => $subForum, 'pinned' => false],
        ['id' => '2ff35c57-46b3-4031-b144-9bbbfe00207d', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'a1d6aaac-2288-4f3a-85cd-f1ed4be9b3db', 'forum' => $subForum, 'pinned' => false],
        ['id' => '782004ba-c9e3-41b0-98c8-184e8596fea6', 'forum' => $subForum, 'pinned' => false],
        ['id' => '3d572d15-087e-4ea0-9b40-f79241d2bcd5', 'forum' => $subForum, 'pinned' => false],
        ['id' => '91903b51-8007-4226-a622-956dec909a80', 'forum' => $subForum, 'pinned' => false],
        ['id' => '21d3bdc1-17d7-45a6-acc0-af5ae096be90', 'forum' => $subForum, 'pinned' => false],
        ['id' => '95fa5e16-db50-4c70-b5eb-0a299a7af1d8', 'forum' => $subForum, 'pinned' => true],
        ['id' => '3189e3ba-9c50-406c-a4ea-66da5be2902a', 'forum' => $subForum, 'pinned' => false],
        ['id' => '6844879a-f3e8-4628-889c-41af573c73ca', 'forum' => $subForum, 'pinned' => false],
        ['id' => '3dadc8cf-eb9d-444b-b12d-7d06d6bcd450', 'forum' => $subForum, 'pinned' => false],
        ['id' => '2621f904-d4e0-44fe-9abb-529384e6351a', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'f2cec334-00a0-4a66-a133-013ffa70caf6', 'forum' => $subForum, 'pinned' => false],
        ['id' => '937ef2c2-6bd3-4045-8bf7-d0f19820d2e2', 'forum' => $subForum, 'pinned' => false],
        ['id' => '52deb80c-143f-48ab-81f9-f8d39fb81cea', 'forum' => $subForum, 'pinned' => false],
        ['id' => '46600e17-5cd2-4e7d-b8cf-2949457deaab', 'forum' => $subForum, 'pinned' => false],
        ['id' => '60079302-b87c-4e4a-b0d9-59bef0d4c91f', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'b1010583-d0a7-453e-8f6d-a295e3d9e5b9', 'forum' => $subForum, 'pinned' => false],
        ['id' => '4e55e6b9-e047-41a1-8d53-ed3b2ba9881c', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'e6994eb7-9a4d-4030-a325-8dad1bcb3bb4', 'forum' => $subForum, 'pinned' => false],
        ['id' => '63ec97d4-e6b0-4898-964f-2596228bd062', 'forum' => $subForum, 'pinned' => false],
        ['id' => '195e0e5f-e53b-4e2f-aea1-617a1ac5d636', 'forum' => $subForum, 'pinned' => false],
        ['id' => 'c8489a9d-0f8d-4811-a677-d27e18b1d764', 'forum' => $subForum, 'pinned' => false],
        ['id' => '230940f4-613b-455c-9ca7-55a9f6402424', 'forum' => $subForum, 'pinned' => false],
        ['id' => '99437f04-b94c-49c6-8e41-6f79de421605', 'forum' => $subForum2, 'pinned' => false],
    ];

    $createThread = function (string $id, int $index, SubForum $forum, bool $pinned) use ($bob, $threadEncryptor): Thread {
        $encrypted = $threadEncryptor->encrypt($id, new HiddenString("thread $index"));
        $date = new DateTimeImmutable('1990-01-02 03:04:05');
        $date = $date->add(new DateInterval("P{$index}Y"));

        return new Thread(
            $id,
            $forum,
            $bob,
            $encrypted,
            $date,
            $pinned,
        );
    };

    $threads = [];
    foreach ($threadConfig as $index => $config) {
        $threads[] = $createThread($config['id'], $index, $config['forum'], $config['pinned']);
    }

    return [
        $bob,
        $subForum,
        $subForum2,
        ...$threads,
    ];
};
