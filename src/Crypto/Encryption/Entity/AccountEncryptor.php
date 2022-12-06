<?php

declare(strict_types=1);

namespace App\Crypto\Encryption\Entity;

use App\Model\Crypto\Encryption\Entity\AccountDecryptionResult;
use App\Model\Crypto\Encryption\Entity\AccountEncryptionResult;
use App\Model\Entity\Account;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\DecryptionFailure;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\EncryptionFailure;
use App\Model\Primitive\EncryptedString;
use App\Model\Primitive\HashedString;
use ParagonIE\CipherSweet\Backend\ModernCrypto;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Exception\ArrayKeyException;
use ParagonIE\CipherSweet\Exception\CryptoOperationException;
use ParagonIE\CipherSweet\KeyProvider\StringProvider;
use ParagonIE\HiddenString\HiddenString;
use Psr\Log\LoggerInterface;
use SodiumException;

class AccountEncryptor
{
    private readonly LoggerInterface $logger;
    private readonly CipherSweet $cipherSweet;
    private ?EncryptedRow $row;

    public function __construct(
        LoggerInterface $logger,
        #[SensitiveParameter] string $encryptionKey,
    ) {
        $this->logger = $logger;

        $this->row = null;
        $this->cipherSweet = new CipherSweet(
            new StringProvider($encryptionKey),
            new ModernCrypto(),
        );
    }

    /**
     * @param string $id
     * @param HiddenString $emailAddress
     *
     * @return AccountEncryptionResult
     *
     * @throws EncryptionFailure
     */
    public function encrypt(string $id, HiddenString $emailAddress): AccountEncryptionResult
    {
        $row = $this->getRowConfig();

        try {
            $encryptedData = $row->prepareRowForStorage(
                [
                    'id' => $id,
                    'email_address' => $emailAddress->getString(),
                ],
            );
        } catch (ArrayKeyException|CryptoOperationException|SodiumException $e) {
            throw EncryptionFailure::because($e);
        }

        return new AccountEncryptionResult(
            new EncryptedString($encryptedData[0]['email_address']),
            new HashedString($encryptedData[1]['email_address_bidx']['value']),
        );
    }

    /**
     * @param Account $account
     *
     * @return AccountDecryptionResult
     *
     * @throws DecryptionFailure
     */
    public function decrypt(Account $account): AccountDecryptionResult
    {
        $row = new EncryptedRow($this->cipherSweet, 'account');
        $row->addTextField('email_address', 'id');

        try {
            $decryptedData = $row->decryptRow(
                [
                    'id' => $account->getId(),
                    'email_address' => $account->getEmailAddress()->value,
                ],
            );
        } catch (CryptoOperationException|SodiumException $e) {
            throw DecryptionFailure::because($e);
        }

        $this->logger->notice("Account [{$account->getId()}] decrypted");

        return new AccountDecryptionResult(
            new HiddenString($decryptedData['email_address']),
        );
    }

    private function getRowConfig(): EncryptedRow
    {
        if ($this->row === null) {
            $this->row = new EncryptedRow($this->cipherSweet, 'account');
            $this->row->addTextField('id');
            $this->row->addTextField('email_address', 'id');
            $this->row->addBlindIndex(
                'email_address',
                new BlindIndex('email_address_bidx'),
            );
        }

        return $this->row;
    }
}
