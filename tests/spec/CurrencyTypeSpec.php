<?php declare(strict_types=1);

namespace spec\Amberovsky\Money\Currency\Doctrine;

use Amberovsky\Money\Currency\Currency;
use Amberovsky\Money\Currency\CurrencyFactory;
use Amberovsky\Money\Currency\Doctrine\CurrencyType;
use Amberovsky\Money\Currency\Exception\UnknownNumericCodeCurrencyException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;

class CurrencyTypeSpec extends ObjectBehavior {
    public function it_returns_name() {
        $this->getName()->shouldReturn((string) CurrencyType::NAME);
    }

    public function it_returns_null_in_convertToPHPValue_when_null_passed(AbstractPlatform $abstractPlatform) {
        $this->convertToPHPValue(null, $abstractPlatform)->shouldReturn(null);
    }

    public function it_returns_null_in_convertToPHPValue_when_empty_passed(AbstractPlatform $abstractPlatform) {
        $this->convertToPHPValue('', $abstractPlatform)->shouldReturn(null);
    }

    public function it_returns_object_in_convertToPHPValue_when_Currency_passed(Currency $currency, AbstractPlatform $abstractPlatform) {
        $this->convertToPHPValue($currency, $abstractPlatform)->shouldReturn($currency);
    }

    public function it_throws_ConversionException_in_convertToPHPValue_when_nonint_passed(AbstractPlatform $abstractPlatform) {
        $this->shouldThrow(ConversionException::class)->duringConvertToPHPValue("abc", $abstractPlatform);
    }

    public function it_throws_ConversionException_in_convertToPHPValue_when_less_than_1_passed(AbstractPlatform $abstractPlatform) {
        $this->shouldThrow(ConversionException::class)->duringConvertToPHPValue(-1, $abstractPlatform);
    }

    public function it_throws_ConversionException_in_convertToPHPValue_when_greater_than_999_passed(AbstractPlatform $abstractPlatform) {
        $this->shouldThrow(ConversionException::class)->duringConvertToPHPValue(1000, $abstractPlatform);
    }

    public function it_calls_CurrencyFactory_in_convertToPHPValue(
        CurrencyFactory $currencyFactory, Currency $currency, AbstractPlatform $abstractPlatform
    ) {
        $currencyFactory
            ->fromNumericCode(777)
            ->shouldBeCalled()
            ->willReturn($currency);

        $this->setCurrencyFactory($currencyFactory);

        $this->convertToPHPValue(777, $abstractPlatform)->shouldReturn($currency);
    }

    public function it_throws_ConversionException_in_convertToPHPValue_when_CurrencyException_was_thrown(
        CurrencyFactory $currencyFactory, AbstractPlatform $abstractPlatform
    ) {
        $currencyFactory
            ->fromNumericCode(Argument::any())
            ->shouldBeCalled()
            ->willThrow(new UnknownNumericCodeCurrencyException("777"));

        $this->setCurrencyFactory($currencyFactory);

        $this->shouldThrow(ConversionException::class)->duringConvertToPHPValue(777, $abstractPlatform);
    }

    public function it_returns_null_in_convertToDatabaseValue_when_null_passed(AbstractPlatform $abstractPlatform) {
        $this->convertToDatabaseValue(null, $abstractPlatform)->shouldReturn(null);
    }

    public function it_returns_null_in_convertToDatabaseValue_when_empty_passed(AbstractPlatform $abstractPlatform) {
        $this->convertToDatabaseValue('', $abstractPlatform)->shouldReturn(null);
    }

    public function it_returns_numericCode_in_convertToDatabaseValue(
        Currency $currency, AbstractPlatform $abstractPlatform
    ) {
        $currency->getNumericCode()->shouldBeCalled()->willReturn(777);
        $this->convertToDatabaseValue($currency, $abstractPlatform)->shouldReturn("777");
    }

    public function it_throws_ConversionException_in_convertToDatabaseValue_when_non_Currency_passed(
        stdClass $stdClass, AbstractPlatform $abstractPlatform
    ) {
        $this->shouldThrow(ConversionException::class)->duringConvertToDatabaseValue($stdClass, $abstractPlatform);
    }

    public function it_requires_sql_comment_hint(AbstractPlatform $platform) {
        $this->requiresSQLCommentHint($platform)->shouldReturn(true);
    }
}
