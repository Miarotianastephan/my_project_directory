<?php

namespace App\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\Deprecations\Deprecation;

use function get_class;

/**
 * Type that maps an SQL DATE to a PHP Date object.
 */
class CustomDateType extends DateType
{
    const CUSTOMDATE = 'customdate'; // Nom unique du type

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Utilisez la déclaration SQL de DateType
        return $platform->getDateTypeDeclarationSQL($fieldDeclaration);
    }

    
    /**
     * {@inheritDoc}
     *
     * @psalm-param T $value
     *
     * @return (T is null ? null : string)
     *
     * @template T
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            if ($value instanceof DateTimeImmutable) {
                Deprecation::triggerIfCalledFromOutside(
                    'doctrine/dbal',
                    'https://github.com/doctrine/dbal/pull/6017',
                    'Passing an instance of %s is deprecated, use %s::%s() instead.',
                    get_class($value),
                    DateImmutableType::class,
                    __FUNCTION__,
                );
            }

            return $value->format('d-M-Y');
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', DateTime::class]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof DateTimeImmutable) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https://github.com/doctrine/dbal/pull/6017',
                'Passing an instance of %s is deprecated, use %s::%s() instead.',
                get_class($value),
                DateImmutableType::class,
                __FUNCTION__,
            );
        }

        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        $dateTime = DateTime::createFromFormat('d-M-Y', $value);
        if ($dateTime !== false) {
            return $dateTime;
        }

        throw ConversionException::conversionFailedFormat(
            $value,
            $this->getName(),
            $platform->getDateFormatString(),
        );
    }

    public function getName()
    {
        return self::CUSTOMDATE; // Retourne le nom unique de ce type
    }
}
