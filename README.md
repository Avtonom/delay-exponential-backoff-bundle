Avtonom Exponential backoff to delay
==============================

Exponential backoff is an algorithm that uses feedback to multiplicatively decrease the rate of some process, in order to gradually find an acceptable rate.

Page bundle: https://github.com/Avtonom/delay-exponential-backoff-bundle

```php

    $ php bin/console exponential-backoff
    $ php bin/console exponential-backoff halfDelay -l 20
    
```

attempt | microseconds | seconds | minutes | hours
1 | 150000000 | 150 | 2.5 | 0.04
|2|300000000|300|5|0.08|
|3|600000000|600|10|0.17|
|4|1200000000|1200|20|0.33|
|5|2400000000|2400|40|0.67|
|6|4800000000|4800|80|1.33|
|7|9600000000|9600|160|2.67|
|8|19200000000|19200|320|5.33|
|9|38400000000|38400|640|10.67|
|10|76800000000|76800|1280|21.33|
|11|153600000000|153600|2560|42.67|

#### To Install

Run the following in your project root, assuming you have composer set up for your project

```sh

composer.phar require avtonom/exponential-backoff-bundle ~1.1

```

Switching `~1.1` for the most recent tag.

Add the bundle to app/AppKernel.php

```php

$bundles(
    ...
            new Avtonom\ExponentialBackoffBundle\AvtonomExponentialBackoffBundle(),
    ...
);

```

Configuration options (parameters.yaml):

``` yaml

parameters:
    avtonom_exponential_backoff.cap: 1000000 # [OPTIONAL] - Max duration allowed (in microseconds). If backoff duration is greater than cap, cap is returned
    avtonom_exponential_backoff.max_attempts: 0 # [OPTIONAL] - Number of attemps before thrown an Exception
    
```

# API

### getDefaultOptions():

This method is static and returns an array with the default options:
- `cap`: Max duration allowed (in microseconds). If backoff duration is greater than cap, cap is returned, default is `1000000` microseconds.
- `maxAttempts`: Number of attempts before thrown an Yriveiro\Backoff\BackoffException. Default is `0`, no limit.

### halfDelay($attempt):

### delay($attempt):

### exponential($attempt):

This method use and exponential function `E(attempt) = (2**attempt - 1)` to calculate backoff time.

#### Parameters
- `attempt`: incremental value that represents the current retry number.

### equalJitter($attempt);

Exponential backoff has one disadvantage. In high concurrence, we can have multiples calls with the same backoff time due the time is highly bound to the current attempt, different calls could be in the same attempt.

To solve this we can add a jitter value to allow some randomization.

`equalJitter` uses the function: `E(attempt) = min(((2**attempt - 1) / 2), random(0, ((2**attempt - 1) / 2)))`.

#### Parameters
- `attempt`: incremental value that represents the current retry number.

### fullJitter($attempt);

Full jitter behaves like `equalJitter` method, the main difference between them is the way in how the jitter value is calculated.

`fullJitter` uses the function: `E(attempt) = min(random(0, (2**attempt - 1) / 2))`.

#### Parameters
- `attempt`: incremental value that represents the current retry number.

# Usage


```php

    $attempt++;
    usleep($this->('avtonom_exponential_backoff')->equalJitter($attempt));
    
```


Read information https://habrahabr.ru/post/227225/

Use https://github.com/yriveiro/php-backoff
