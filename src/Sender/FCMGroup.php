<?php
declare(strict_types = 1);

namespace LaravelFCM\Sender;

use LaravelFCM\Request\GroupRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FCMGroup
 *
 * @package LaravelFCM\Sender
 */
class FCMGroup extends HTTPSender
{
    public const CREATE = 'create';
    public const ADD = 'add';
    public const REMOVE = 'remove';

    /**
     * Create a group.
     *
     * @param $notificationKeyName
     * @param array $registrationIds
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createGroup($notificationKeyName, array $registrationIds): ?string
    {
        $request = new GroupRequest(self::CREATE, $notificationKeyName, null, $registrationIds);

        $response = $this->client->request('post', $this->url, $request->build());

        return $this->getNotificationToken($response);
    }

    /**
     * Add registrationId to a existing group.
     *
     * @param $notificationKeyName
     * @param $notificationKey
     * @param array $registrationIds
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addToGroup($notificationKeyName, $notificationKey, array $registrationIds): ?string
    {
        $request = new GroupRequest(self::ADD, $notificationKeyName, $notificationKey, $registrationIds);
        $response = $this->client->request('post', $this->url, $request->build());

        return $this->getNotificationToken($response);
    }

    /**
     * Remove registrationId to a existing group.
     * Note: if you remove all registrationIds the group is automatically deleted
     *
     * @param $notificationKeyName
     * @param $notificationKey
     * @param array $registeredIds
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeFromGroup($notificationKeyName, $notificationKey, array $registeredIds): ?string
    {
        $request = new GroupRequest(self::REMOVE, $notificationKeyName, $notificationKey, $registeredIds);
        $response = $this->client->request('post', $this->url, $request->build());

        return $this->getNotificationToken($response);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return null|string
     * @internal
     *
     */
    private function getNotificationToken(ResponseInterface $response): ?string
    {
        if (! $this->isValidResponse($response)) {
            return null;
        }

        $json = json_decode($response->getBody()->getContents(), true);

        return $json['notification_key'];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function isValidResponse(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 200;
    }
}
