<?php
declare(strict_types = 1);

namespace LaravelFCM\Response;

use LaravelFCM\Message\Topics;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TopicResponse
 *
 * @package LaravelFCM\Response
 */
class TopicResponse extends BaseResponse implements TopicResponseContract
{
    public const LIMIT_RATE_TOPICS_EXCEEDED = 'TopicsMessageRateExceeded';

    /**
     * @internal
     *
     * @var string
     */
    protected $topic;

    /**
     * @internal
     *
     * @var string
     */
    protected $messageId;

    /**
     * @internal
     *
     * @var string|null
     */
    protected $error;

    /**
     * @internal
     *
     * @var bool
     */
    protected $needRetry = false;

    /**
     * TopicResponse constructor.
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \LaravelFCM\Message\Topics $topic
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function __construct(ResponseInterface $response, Topics $topic)
    {
        $this->topic = $topic;
        parent::__construct($response);
    }

    /**
     * parse the response.
     *
     * @param $responseInJson
     *
     * @return void
     */
    protected function parseResponse($responseInJson): void
    {
        if (!$this->parseSuccess($responseInJson)) {
            $this->parseError($responseInJson);
        }

        if ($this->logEnabled) {
            $this->logResponse();
        }
    }

    /**
     * @param $responseInJson
     *
     * @return void
     * @internal
     *
     */
    private function parseSuccess($responseInJson): void
    {
        if (array_key_exists(self::MESSAGE_ID, $responseInJson)) {
            $this->messageId = $responseInJson[ self::MESSAGE_ID ];
        }
    }

    /**
     * @param $responseInJson
     *
     * @return void
     * @internal
     *
     */
    private function parseError($responseInJson): void
    {
        if (array_key_exists(self::ERROR, $responseInJson)) {
            if (in_array(self::LIMIT_RATE_TOPICS_EXCEEDED, $responseInJson, true)) {
                $this->needRetry = true;
            }

            $this->error = $responseInJson[ self::ERROR ];
        }
    }

    /**
     * Log the response.
     *
     * @return void
     * @throws \Exception
     */
    protected function logResponse(): void
    {
        $logger = new Logger('Laravel-FCM');
        $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-fcm.log')));

        $topic = $this->topic->build();

        $logMessage = 'notification send to topic: ' . json_encode($topic);
        if ($this->messageId) {
            $logMessage .= "with success (message-id : $this->messageId)";
        } else {
            $logMessage .= "with error (error : $this->error)";
        }

        $logger->info($logMessage);
    }

    /**
     * true if topic sent with success.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return (bool) $this->messageId;
    }

    /**
     * return error message
     * you should test if it's necessary to resent it.
     *
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * return true if it's necessary resent it using exponential backoff.
     *
     * @return bool
     */
    public function shouldRetry(): bool
    {
        return $this->needRetry;
    }
}
