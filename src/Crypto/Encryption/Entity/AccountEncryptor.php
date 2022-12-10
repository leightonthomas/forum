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
use App\Transformer\Crypto\CipherSweet\PaddingTransformer;
use ParagonIE\CipherSweet\Backend\ModernCrypto;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Exception\ArrayKeyException;
use ParagonIE\CipherSweet\Exception\CryptoOperationException;
use ParagonIE\CipherSweet\KeyProvider\StringProvider;
use ParagonIE\CipherSweet\Transformation\Lowercase;
use ParagonIE\HiddenString\HiddenString;
use Psr\Log\LoggerInterface;
use SensitiveParameter;
use SodiumException;

/**
 * @psalm-type _EncryptedData = array{
 *     0: array{
 *         username: string,
 *         email_address: string,
 *     },
 *     1: array{
 *         username_bidx: array{value: string},
 *         email_address_bidx: array{value: string},
 *     },
 * }
 */
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
     * @param HiddenString $username
     * @param HiddenString $emailAddress
     *
     * @return AccountEncryptionResult
     *
     * @throws EncryptionFailure
     */
    public function encrypt(string $id, HiddenString $username, HiddenString $emailAddress): AccountEncryptionResult
    {
        $row = $this->getRowConfig();

        try {
            /** @var _EncryptedData $encryptedData */
            $encryptedData = $row->prepareRowForStorage(
                [
                    'id' => $id,
                    'username' => $username->getString(),
                    'email_address' => $emailAddress->getString(),
                ],
            );
        } catch (ArrayKeyException|CryptoOperationException|SodiumException $e) {
            throw EncryptionFailure::because($e);
        }

        return new AccountEncryptionResult(
            new EncryptedString($encryptedData[0]['username']),
            new HashedString($encryptedData[1]['username_bidx']['value']),
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
        $row->addTextField('username', 'id');

        try {
            $decryptedData = $row->decryptRow(
                [
                    'id' => $account->getId(),
                    'username' => $account->getUsername()->value,
                    'email_address' => $account->getEmailAddress()->value,
                ],
            );
        } catch (CryptoOperationException|SodiumException $e) {
            throw DecryptionFailure::because($e);
        }

        $this->logger->notice("Account [{$account->getId()}] decrypted");

        /** @var string $emailAddress */
        $emailAddress = $decryptedData['email_address'];
        /** @var string $username */
        $username = $decryptedData['username'];

        return new AccountDecryptionResult(
            new HiddenString($username),
            new HiddenString($emailAddress),
        );
    }

    private function getRowConfig(): EncryptedRow
    {
        if ($this->row === null) {
            $this->row = new EncryptedRow($this->cipherSweet, 'account');
            $this->row->addTextField('id');
            $this->row->addTextField('email_address', 'id');
            $this->row->addTextField('username', 'id');
            $this->row->addBlindIndex(
                'email_address',
                new BlindIndex('email_address_bidx', [new Lowercase(), new PaddingTransformer(255)]),
            );
            $this->row->addBlindIndex(
                'username',
                new BlindIndex('username_bidx', [new Lowercase(), new PaddingTransformer(255)]),
            );
        }

        return $this->row;
    }
}
