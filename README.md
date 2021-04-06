# DeductiveFormula

DeductiveFormula is library evaluate expression of variables from given string

### Install via "composer require"
```shell
composer require rizalmf/deductive-formula
```

### Usage:

```php

use rizalmf\formula\DeductiveFormula;
use rizalmf\formula\exception\FormulaException;

require_once __DIR__ . '/vendor/autoload.php'; // example path

$formula = new DeductiveFormula();
```

Example 1: (Define Expressions & Set Formula)
```php
try {
    $expression = '{foo}^({bar}/2+(2+3^(1/2)))';
    $requestedVariables = $formula->setFormula($expression);
    var_dump($formula->getFormula());
    var_dump($requestedVariables);

} catch (FormulaException $e) {
    // handle Exceptions
}

// output : 
// {foo}^({bar}/2+(2+3^(1/2)))
// array ([0] => "foo", [1] => "bar")
```

Example 2: (Set Variables)
```php
    // ...

    // 1. define value per variable
    $formula->setVariable("foo", 4);
    $formula->setVariable("bar", 1);

    // 2. use array
    $formula->setVariables([
        "foo" => 4,
        "bar" => 1
    ]);
```
    
Example 3: (Get Variables)
```php
    // ...

    // 1. get value from specific variable
    var_dump($formula->getVariable("foo"));
    // output : 4

    // 2. get values all variables
    var_dump($formula->getVariables());
    // output : array ([foo] => 4, [bar] => 1)

    // 1. get possible variables from Formula
    var_dump($formula->getRequestedVariables());
    // output : array ([0] => "foo", [1] => "bar")

```

Example 4: (Evaluate)
```php
    $result = $formula->execute();
    var_dump($result);

    // output : 353.141268350837
```

Example 5: (Set Limit Iteration & Debug)
```php
    // (optional) set limit iteration calculating machine. default 200
    $formula->setLimit(50);

    // show prepared formula
    var_dump($formula->getFormulaExposed());
    // output : 4^(1/2+(2+3^(1/2)))

    // debug
    $result = $formula->execute(true);
    var_dump($result);
    // output : 
    // iteration-1 => 4^(1/2+(2+3^(1/2)))
    // iteration-2 => 4^(1/2+(2+3^0.50))
    // iteration-3 => 4^(1/2+(2+1.73205080756888))
    // iteration-4 => 4^(1/2+3.73205080756888)
    // iteration-5 => 4^(0.50+3.73205080756888)
    // iteration-6 => 4^4.23205080756888
    // 353.141268350837
```

Exception Tree
```php
    FormulaException
        |- BadFormulaException
```

### Unit Test:

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit test
```

## LICENSE

The MIT License (MIT)
Copyright (c) 2021 Rizal Maulana Fahmi