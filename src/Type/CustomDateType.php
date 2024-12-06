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
 * Type personnalisé pour mapper un champ SQL DATE à un objet PHP DateTime.
 *
 * Cette classe permet de manipuler des objets `DateTime` dans Doctrine en utilisant un format personnalisé pour la base de données.
 * Elle hérite de `Doctrine\DBAL\Types\DateType` et remplace certaines méthodes pour appliquer un format spécifique lors de la conversion vers et depuis la base de données.
 */
class CustomDateType extends DateType
{
    const CUSTOMDATE = 'customdate'; // Nom unique du type

    /**
     * {@inheritDoc}
     *
     * Déclare la représentation SQL du type.
     *
     * @param array $fieldDeclaration Déclaration des champs.
     * @param AbstractPlatform $platform Plateforme SQL utilisée.
     * @return string La déclaration SQL du type.
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Utilise la déclaration SQL du type DateType de Doctrine
        return $platform->getDateTypeDeclarationSQL($fieldDeclaration);
    }


    /**
     * {@inheritDoc}
     *
     * Convertit une valeur PHP en une valeur SQL pour le stockage dans la base de données.
     *
     * @param mixed $value La valeur à convertir.
     * @param AbstractPlatform $platform La plateforme de base de données.
     *
     * @return string|null La valeur formatée pour la base de données (ou null si la valeur est null).
     *
     * @throws ConversionException Si la conversion échoue (type incorrect).
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            // Si c'est une instance de DateTimeImmutable, déclenche un avertissement de dépréciation
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

    /**
     * {@inheritDoc}
     *
     * Retourne le nom unique du type personnalisé.
     *
     * @return string Le nom du type.
     */
    public function getName()
    {
        return self::CUSTOMDATE; // Retourne le nom unique de ce type
    }

    /**
     * {@inheritDoc}
     *
     * Convertit une valeur SQL en une valeur PHP pour son utilisation dans l'application.
     *
     * @param mixed $value La valeur à convertir.
     * @param AbstractPlatform $platform La plateforme de base de données.
     *
     * @return DateTime|null L'objet DateTime créé à partir de la valeur.
     *
     * @throws ConversionException Si la conversion échoue (format incorrect).
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // Si c'est une instance de DateTimeImmutable, déclenche un avertissement de dépréciation
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

        // Si la valeur est déjà une instance de DateTimeInterface ou null, la retourne directement
        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        // Si la valeur est une chaîne de caractères, tente de la convertir en un objet DateTime
        // Le format attendu est 'd-M-y' (ex: 01-Jan-24)
        $dateTime = DateTime::createFromFormat('d-M-y', $value);
        if ($dateTime !== false) {
            return $dateTime;
        }

        // Si la conversion échoue, lance une exception
        throw ConversionException::conversionFailedFormat(
            $value,
            $this->getName(),
            $platform->getDateFormatString(),
        );
    }
}
