<?php
declare(strict_types = 1);

namespace LaravelFCM\Response\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UnauthorizedRequestException
 *
 * @package LaravelFCM\Response\Exceptions
 */
class UnauthorizedRequestException extends Exception
{
    /**
     * UnauthorizedRequestException constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $code = $response->getStatusCode();

        parent::__construct('FCM_SENDER_ID or FCM_SERVER_KEY are invalid', $code);
    }
}
