<?php
declare(strict_types = 1);

namespace LaravelFCM\Request;

use LaravelFCM\Message\Options;
use LaravelFCM\Message\PayloadData;
use LaravelFCM\Message\PayloadNotification;
use LaravelFCM\Message\Topics;

/**
 * Class Request
 *
 * @package LaravelFCM\Request
 */
class Request extends BaseRequest
{
    /**
     * @internal
     *
     * @var string|array
     */
    protected $to;

    /**
     * @internal
     *
     * @var Options
     */
    protected $options;

    /**
     * @internal
     *
     * @var PayloadNotification
     */
    protected $notification;

    /**
     * @internal
     *
     * @var PayloadData
     */
    protected $data;

    /**
     * @internal
     *
     * @var Topics|null
     */
    protected $topic;

    /**
     * Request constructor.
     *
     * @param                     $to
     * @param Options             $options
     * @param PayloadNotification $notification
     * @param PayloadData         $data
     * @param Topics|null         $topic
     */
    public function __construct($to, Options $options = null, PayloadNotification $notification = null, PayloadData $data = null, Topics $topic = null)
    {
        parent::__construct();

        $this->to = $to;
        $this->options = $options;
        $this->notification = $notification;
        $this->data = $data;
        $this->topic = $topic;
    }

    /**
     * Build the body for the request.
     *
     * @return array
     * @throws \LaravelFCM\Message\Exceptions\NoTopicProvidedException
     */
    protected function buildBody(): array
    {
        $message = [
            'to' => $this->getTo(),
            'registration_ids' => $this->getRegistrationIds(),
            'notification' => $this->getNotification(),
            'data' => $this->getData(),
        ];

        $message = array_merge($message, $this->getOptions());

        // remove null entries
        return array_filter($message);
    }

    /**
     * Get to key transformed.
     *
     * @return array|string|null
     * @throws \LaravelFCM\Message\Exceptions\NoTopicProvidedException
     */
    protected function getTo()
    {
        $to = is_array($this->to) ? null : $this->to;

        if ($this->topic && $this->topic->hasOnlyOneTopic()) {
            $to = $this->topic->build();
        }

        return $to;
    }

    /**
     * Get registrationIds transformed.
     *
     * @return array|null
     */
    protected function getRegistrationIds(): ?array
    {
        return is_array($this->to) ? $this->to : null;
    }

    /**
     * Get Options transformed.
     *
     * @return array
     * @throws \LaravelFCM\Message\Exceptions\NoTopicProvidedException
     */
    protected function getOptions(): array
    {
        $options = $this->options ? $this->options->toArray() : [];

        if ($this->topic && !$this->topic->hasOnlyOneTopic()) {
            $options = array_merge($options, $this->topic->build());
        }

        return $options;
    }

    /**
     * get notification transformed.
     *
     * @return array|null
     */
    protected function getNotification(): ?array
    {
        return $this->notification ? $this->notification->toArray() : null;
    }

    /**
     * get data transformed.
     *
     * @return array|null
     */
    protected function getData(): ?array
    {
        return $this->data ? $this->data->toArray() : null;
    }
}
