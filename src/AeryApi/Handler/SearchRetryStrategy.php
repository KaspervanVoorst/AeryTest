<?php

namespace App\AeryApi\Handler;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;

class SearchRetryStrategy implements RetryStrategyInterface
{

    /**
     * @param Envelope $message
     * @param \Throwable|null $throwable
     * @return bool
     */
    public function isRetryable(Envelope $message, ?\Throwable $throwable = null): bool
    {
        if ($throwable->getCode() === 429) { // HTTP_TOO_MANY_REQUESTS
            return true;
        }

        return false;
    }

    /**
     * @param Envelope $message
     * @param \Throwable|null $throwable
     * @return int
     */
    public function getWaitingTime(Envelope $message, ?\Throwable $throwable = null): int
    {
        return 300000;
    }
}
