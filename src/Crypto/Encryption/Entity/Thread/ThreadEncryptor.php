<?php

declare(strict_types=1);

namespace App\Crypto\Encryption\Entity\Thread;

use App\Model\Entity\Thread\Thread;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\DecryptionFailure;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\EncryptionFailure;
use App\Model\Primitive\EncryptedString;
use ParagonIE\CipherSweet\Backend\ModernCrypto;
use ParagonIE\CipherSweet\CipherSweet;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Exception\ArrayKeyException;
use ParagonIE\CipherSweet\Exception\CryptoOperationException;
use ParagonIE\CipherSweet\KeyProvider\StringProvider;
use ParagonIE\HiddenString\HiddenString;
use Psr\Log\LoggerInterface;
use SensitiveParameter;
use SodiumException;

/**
 * @psalm-type _EncryptedData = array{
 *     0: array{
 *         name: string,
 *     },
 * }
 */
class ThreadEncryptor
{
    private readonly LoggerInterface $logger;
    private readonly CipherSweet $cipherSweet;

    public function __construct(
        LoggerInterface $logger,
        #[SensitiveParameter] string $encryptionKey,
    ) {
        $this->logger = $logger;

        $this->cipherSweet = new CipherSweet(
            new StringProvider($encryptionKey),
            new ModernCrypto(),
        );
    }

    /**
     * @param string $id
     * @param HiddenString $name
     *
     * @return EncryptedString
     *
     * @throws EncryptionFailure
     */
    public function encrypt(string $id, HiddenString $name): EncryptedString
    {
        $row = new EncryptedRow($this->cipherSweet, 'thread');
        $row->addTextField('id');
        $row->addTextField('name', 'id');

        try {
            /** @var _EncryptedData $encryptedData */
            $encryptedData = $row->prepareRowForStorage(
                [
                    'id' => $id,
                    'name' => $name->getString(),
                ],
            );
        } catch (ArrayKeyException|CryptoOperationException|SodiumException $e) {
            throw EncryptionFailure::because($e);
        }

        return new EncryptedString($encryptedData[0]['name']);
    }

    /**
     * @param Thread $thread
     *
     * @return HiddenString
     *
     * @throws DecryptionFailure
     */
    public function decrypt(Thread $thread): HiddenString
    {
        $row = new EncryptedRow($this->cipherSweet, 'thread');
        $row->addTextField('name', 'id');

        try {
            $decryptedData = $row->decryptRow(
                [
                    'id' => $thread->getId(),
                    'name' => $thread->getName()->value,
                ],
            );
        } catch (CryptoOperationException|SodiumException $e) {
            throw DecryptionFailure::because($thread, $e);
        }

        $this->logger->notice("Thread [{$thread->getId()}] decrypted");

        /** @var string $name */
        $name = $decryptedData['name'];

        return new HiddenString($name);
    }
}
