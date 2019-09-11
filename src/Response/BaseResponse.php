<?php

namespace LaravelFCM\Response;

use LaravelFCM\Response\Exceptions\InvalidRequestException;
use LaravelFCM\Response\Exceptions\ServerResponseException;
use LaravelFCM\Response\Exceptions\UnauthorizedRequestException;
use Psr\Http\Message\ResponseInterface;
use function config;

/**
 * Class BaseResponse.
 */
abstract class BaseResponse
{
    public const SUCCESS = 'success';
    public const FAILURE = 'failure';
    public const ERROR = 'error';
    public const MESSAGE_ID = 'message_id';

    /**
     * @var bool
     */
    protected $logEnabled = false;

    /**
     * BaseResponse constructor.
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function __construct(ResponseInterface $response)
    {
        $this->isJsonResponse($response);
        $this->logEnabled = config('fcm.log_enabled', false);
        $responseInJson = json_decode($response->getBody(), true);
        $this->parseResponse($responseInJson);
    }

    /**
     * Check if the response given by fcm is parsable.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws InvalidRequestException
     * @throws ServerResponseException
     * @throws UnauthorizedRequestException
     */
    private function isJsonResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() === 200) {
            return;
        }

        if ($response->getStatusCode() === 400) {
            throw new InvalidRequestException($response);
        }

        if ($response->getStatusCode() === 401) {
            throw new UnauthorizedRequestException($response);
        }

        throw new ServerResponseException($response);
    }

    /**
     * parse the response.
     *
     * @param array $responseInJson
     */
    abstract protected function parseResponse($responseInJson);

    /**
     * Log the response.
     */
    abstract protected function logResponse();
}
