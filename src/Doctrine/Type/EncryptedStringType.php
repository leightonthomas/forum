<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Model\Primitive\EncryptedString;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class EncryptedStringType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): EncryptedString
    {
        return new EncryptedString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (! ($value instanceof EncryptedString)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return (string) $value;
    }

    public function getName(): string
    {
        return 'encrypted_string';
    }
}
