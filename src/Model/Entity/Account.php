<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use App\Repository\Entity\DoctrineAccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity(repositoryClass: DoctrineAccountRepository::class)]
#[ORM\Table(indexes: [new Index(columns: ['email_address_bidx'], name: 'email_address_bidx_idx')])]
final class Account implements Persistent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'string', length: 30, unique: true)]
    private string $username;

    #[ORM\Column(type: 'encrypted_string')]
    private EncryptedString $emailAddress;

    #[ORM\Column(name: 'email_address_bidx', type: 'hashed_string', unique: true)]
    private HashedString $emailAddressBlindIndex;

    #[ORM\Column(type: 'hashed_string')]
    private HashedString $password;

    private array $claims;

    public function __construct(
        string $id,
        string $username,
        EncryptedString $emailAddress,
        HashedString $emailAddressBlindIndex,
        HashedString $password,
        array $claims,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->emailAddress = $emailAddress;
        $this->emailAddressBlindIndex = $emailAddressBlindIndex;
        $this->password = $password;
        $this->claims = $claims;
    }

    public function changeUsername(string $newUsername): void
    {
        $this->username = $newUsername;
    }

    public function changePassword(HashedString $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function changeEmailAddress(EncryptedString $emailAddress, HashedString $blindIndex): void
    {
        $this->emailAddress = $emailAddress;
        $this->emailAddressBlindIndex = $blindIndex;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmailAddress(): EncryptedString
    {
        return $this->emailAddress;
    }

    public function getPassword(): HashedString
    {
        return $this->password;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }
}
