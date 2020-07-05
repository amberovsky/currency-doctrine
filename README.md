# amberovsky/currency-doctrine

The amberovsky/currency-doctrine package provides the ability to use
[amberovsky/currency](https://github.com/amberovsky/currency) as a [Doctrine field type](https://www.doctrine-project.org/projects/doctrine-dbal/en/2.10/reference/types.html).

## Examples

### Configuration

**It requires CurrencyFactory to be injected in via CurrencyType::setCurrencyFactory method. Use your current DI mechanism to do that.**

To configure Doctrine to use amberovsky/currency as a field type, you'll need to set up the following in your bootstrap:

``` php
\Doctrine\DBAL\Types\Type::addType('Currency', 'Amberovsky\Money\Currency\Doctrine\CurrencyType');
```

In Symfony:

``` yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            Currency: Amberovsky\Money\Currency\Doctrine\CurrencyType
```

In Zend Framework:

```php
<?php
// module.config.php
use Amberovsky\Money\Currency\Doctrine\CurrencyType;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    CurrencyType::NAME => CurrencyType::class,
```

### Usage

Then, in your models, you may annotate properties by setting the `@Column` type to `Currency`.
Doctrine will handle the rest.

``` php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="invoices")
 */
class Invoice
{
    /**
     * @ORM\Column(type="Currency")
     */
    private Currency $currency;

    public function getCurrency(): Currency {
        return $this->currency;
    }
}
```


### How to contribute

Please fork this repo and create a PR. Make sure you run tests before submitting  yout PR:

```shell script
make phpstan
make psalm
make phpspec
```

### License
Copyright (C) 2020 Anton Zagorskii, BSD-3-Clause license, See [license file](/LICENSE.txt) for details
