<?php
declare(strict_types = 1);

namespace LaravelFCM\Response;

/**
 * Interface TopicResponseContract
 *
 * @package LaravelFCM\Response
 */
interface TopicResponseContract
{
    /**
     * true if topic sent with success.
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * return error message
     * you should test if it's necessary to resent it.
     *
     * @return string|null
     */
    public function error(): ?string;

    /**
     * return true if it's necessary resent it using exponential backoff.
     *
     * @return bool
     */
    public function shouldRetry(): bool;
}
