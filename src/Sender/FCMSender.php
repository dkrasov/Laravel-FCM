<?php
declare(strict_types = 1);

namespace LaravelFCM\Sender;

use GuzzleHttp\Exception\ClientException;
use LaravelFCM\Message\Options;
use LaravelFCM\Message\PayloadData;
use LaravelFCM\Message\PayloadNotification;
use LaravelFCM\Message\Topics;
use LaravelFCM\Request\Request;
use LaravelFCM\Response\DownstreamResponse;
use LaravelFCM\Response\GroupResponse;
use LaravelFCM\Response\TopicResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FCMSender
 *
 * @package LaravelFCM\Sender
 */
class FCMSender extends HTTPSender
{
    public const MAX_TOKEN_PER_REQUEST = 1000;

    /**
     * Send a downstream message to:
     * - a unique device with is registration Token
     * - or to multiples devices with an array of registrationIds
     *
     * @param $to
     * @param \LaravelFCM\Message\Options|null $options
     * @param \LaravelFCM\Message\PayloadNotification|null $notification
     * @param \LaravelFCM\Message\PayloadData|null $data
     *
     * @return \LaravelFCM\Response\DownstreamResponse|null
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function sendTo(
        $to,
        Options $options = null,
        PayloadNotification $notification = null,
        PayloadData $data = null
    ): ?DownstreamResponse {
        $response = null;

        if (is_array($to) && !empty($to)) {
            $partialTokens = array_chunk($to, self::MAX_TOKEN_PER_REQUEST, false);
            foreach ($partialTokens as $tokens) {
                $request = new Request($tokens, $options, $notification, $data);

                $responseGuzzle = $this->post($request);

                $responsePartial = new DownstreamResponse($responseGuzzle, $tokens);
                if (!$response) {
                    $response = $responsePartial;
                } else {
                    $response->merge($responsePartial);
                }
            }
        } else {
            $request = new Request($to, $options, $notification, $data);
            $responseGuzzle = $this->post($request);

            $response = new DownstreamResponse($responseGuzzle, $to);
        }

        return $response;
    }

    /**
     * Send a message to a group of devices identified with them notification key.
     *
     * @param $notificationKey
     * @param \LaravelFCM\Message\Options|null $options
     * @param \LaravelFCM\Message\PayloadNotification|null $notification
     * @param \LaravelFCM\Message\PayloadData|null $data
     *
     * @return \LaravelFCM\Response\GroupResponse
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     */
    public function sendToGroup(
        $notificationKey,
        Options $options = null,
        PayloadNotification $notification = null,
        PayloadData $data = null
    ): GroupResponse {
        $request = new Request($notificationKey, $options, $notification, $data);

        $responseGuzzle = $this->post($request);

        return new GroupResponse($responseGuzzle, $notificationKey);
    }

    /**
     * Send message devices registered at a or more topics.
     *
     * @param \LaravelFCM\Message\Topics $topics
     * @param \LaravelFCM\Message\Options|null $options
     * @param \LaravelFCM\Message\PayloadNotification|null $notification
     * @param \LaravelFCM\Message\PayloadData|null $data
     *
     * @return \LaravelFCM\Response\TopicResponse
     * @throws \LaravelFCM\Response\Exceptions\InvalidRequestException
     * @throws \LaravelFCM\Response\Exceptions\ServerResponseException
     * @throws \LaravelFCM\Response\Exceptions\UnauthorizedRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendToTopic(
        Topics $topics,
        Options $options = null,
        PayloadNotification $notification = null,
        PayloadData $data = null
    ): TopicResponse {
        $request = new Request(null, $options, $notification, $data, $topics);

        $responseGuzzle = $this->post($request);

        return new TopicResponse($responseGuzzle, $topics);
    }

    /**
     * @param $request
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @internal
     */
    protected function post($request): ?ResponseInterface
    {
        try {
            $responseGuzzle = $this->client->request('post', $this->url, $request->build());
        } catch (ClientException $e) {
            $responseGuzzle = $e->getResponse();
        }

        return $responseGuzzle;
    }
}
