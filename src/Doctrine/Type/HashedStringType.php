<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Model\Primitive\HashedString;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class HashedStringType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): HashedString
    {
        return new HashedString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (! ($value instanceof HashedString)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $value->value;
    }

    public function getName(): string
    {
        return 'hashed_string';
    }
}
