<?php

declare(strict_types=1);

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Model\Entity\Account;
use ParagonIE\HiddenString\HiddenString;
use Psr\Container\ContainerInterface;

return static function (ContainerInterface $container): array {
    $hasher = $container->get('crypto.hashing.entity.account.password_hashing_method');
    $hashedPassword = $hasher->hash(new HiddenString('apassword'));

    $encryptor = $container->get(AccountEncryptor::class);

    $ericaEncrypted = $encryptor->encrypt(
        '4231002c-a796-41aa-90b8-947d12a49114',
        new HiddenString('erica'),
        new HiddenString('ERICA@example.com'),
    );
    $bobEncrypted = $encryptor->encrypt(
        '9afbee8b-303f-4582-a260-b6552810bd33',
        new HiddenString('bob'),
        new HiddenString('Bob@ExAmPlE.com'),
    );

    $erica = new Account(
        '4231002c-a796-41aa-90b8-947d12a49114',
        $ericaEncrypted->username,
        $ericaEncrypted->usernameBlindIndex,
        $ericaEncrypted->emailAddress,
        $ericaEncrypted->emailAddressFullBlindIndex,
        $hashedPassword,
        ['ROLE_ADMIN'],
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

    return [
        $erica,
        $bob,
    ];
};
