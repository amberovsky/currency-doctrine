<?php declare(strict_types=1);
/**
 * Copyright (C) Anton Zagorskii
 */

namespace Amberovsky\Money\Currency\Doctrine;

use Amberovsky\Money\Currency\Currency;
use Amberovsky\Money\Currency\CurrencyFactory;
use Amberovsky\Money\Currency\Exception\CurrencyException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * Currency fields will be stored as an unsigned small int in the database and converted back to
 * the Currency value object when querying.
 *
 * This class requires CurrencyFactory to be injected in via setCurrencyFactory method
 */
class CurrencyType extends Type {
    const NAME  = 'Currency';

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private CurrencyFactory $currencyFactory;

    public function setCurrencyFactory(CurrencyFactory $currencyFactory): void {
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @inheritDoc
     *
     * @return Currency|null
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') return null;
        if ($value instanceof Currency) return $value;

        $intValue = (int) $value;
        if (
            (((string) $intValue) !== ((string) $value)) ||
            ($intValue > 999) ||
            ($intValue < 1)
        ) {
            throw ConversionException::conversionFailedFormat((string) $value, $this->getName(), "positive integer [1, 999]");
        }

        try {
            $currency = $this->currencyFactory->fromNumericCode($intValue);
        } catch (CurrencyException $currencyException) {
            throw ConversionException::conversionFailed((string) $value, $this->getName(), $currencyException);
        }

        return $currency;
    }

    /**
     * @inheritDoc
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') return null;

        if ($value instanceof Currency) return (string) $value->getNumericCode();

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [$this->getName()]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return self::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return $platform->getSmallIntTypeDeclarationSQL(array_merge($fieldDeclaration, ['unsigned' => true]));
    }
}
