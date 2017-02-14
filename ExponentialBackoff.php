<?php

namespace Avtonom\ExponentialBackoffBundle;

use Yriveiro\Backoff\BackoffInterface;
use Yriveiro\Backoff\Backoff;

class ExponentialBackoff implements BackoffInterface
{
    /**
     * @var BackoffInterface
     */
    protected $backoff;
    protected $delay;

    /**
     * @param $cap
     * @param $maxAttempts
     */
    public function __construct($cap, $maxAttempts)
    {
        $options = Backoff::getDefaultOptions();
        $options['cap'] = $cap;
        $this->delay = $cap;
        $options['maxAttempts'] = $maxAttempts;
        $this->backoff = new Backoff($options);
    }

    /**
     * Returns an array of Configuration.
     *
     * cap:         Max duration allowed (in microseconds). If backoff duration
     *              is greater than cap, cap is returned.
     * maxAttempts:  Number of attemps before thrown an Yriveiro\Backoff\BackoffException.
     *
     * @return mixed
     */
    public static function getDefaultOptions()
    {
        return Backoff::getDefaultOptions();
    }

    /**
     *
     * Exponential backoff algorithm.
     *
     * c = attempt
     *
     * E(c) = (2**c - 1)
     *
     * @param int $attempt Attempt number.
     *
     * @return float Time to sleep in microseconds before a new retry. The value
     *               is in microseconds to use with usleep, sleep function only
     *               works with seconds
     *
     * @throws InvalidArgumentException.
     * @throws BackoffException
     */
    public function exponential($attempt)
    {
        return $this->backoff->exponential($attempt);
    }

    /**
     * This method adds a half jitter value to exponential backoff value.
     *
     * @param int $attempt Attempt number.
     *
     * @return int
     */
    public function equalJitter($attempt)
    {
        return $this->backoff->equalJitter($attempt);
    }

    /**
     * This method adds a jitter value to exponential backoff value.
     *
     * @param int $attempt Attempt number.
     *
     * @return int
     */
    public function fullJitter($attempt)
    {
        return $this->backoff->fullJitter($attempt);
    }

    /**
     * @param $attempt
     * @return int|number
     */
    public function delay($attempt)
    {
        $delay = $attempt > 1 ? (pow(2, $attempt - 1) * $this->delay) : $this->delay;
        return $delay;
    }

    /**
     * @param $attempt
     * @return int|number
     */
    public function halfDelay($attempt)
    {
        $delay = $attempt > 0 ? (pow(2, $attempt - 2) * $this->delay) : $this->delay;
        return $delay;
    }
}