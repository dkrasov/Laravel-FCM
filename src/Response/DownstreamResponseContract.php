<?php
declare(strict_types = 1);

namespace LaravelFCM\Response;

/**
 * Interface DownstreamResponseContract
 *
 * @package LaravelFCM\Response
 */
interface DownstreamResponseContract
{
    /**
     * Merge two response.
     *
     * @param DownstreamResponse $response
     */
    public function merge(DownstreamResponse $response);

    /**
     * Get the number of device reached with success.
     *
     * @return int
     */
    public function numberSuccess(): int;

    /**
     * Get the number of device which thrown an error.
     *
     * @return int
     */
    public function numberFailure(): int;

    /**
     * Get the number of device that you need to modify their token.
     *
     * @return int
     */
    public function numberModification(): int;

    /**
     * get token to delete.
     *
     * remove all tokens returned by this method in your database
     *
     * @return array
     */
    public function tokensToDelete(): array;

    /**
     * get token to modify.
     *
     * key: oldToken
     * value: new token
     *
     * find the old token in your database and replace it with the new one
     *
     * @return array
     */
    public function tokensToModify(): array;

    /**
     * Get tokens that you should resend using exponential backoof.
     *
     * @return array
     */
    public function tokensToRetry(): array;

    /**
     * Get tokens that thrown an error.
     *
     * key : token
     * value : error
     *
     * In production, remove these tokens from you database
     *
     * @return array
     */
    public function tokensWithError(): array;

    /**
     * check if missing tokens was given to the request
     * If true, remove all the empty token in your database.
     *
     * @return bool
     */
    public function hasMissingToken(): bool;
}
