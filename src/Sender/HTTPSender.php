<?php
declare(strict_types = 1);

namespace LaravelFCM\Sender;

use GuzzleHttp\ClientInterface;

/**
 * Class HTTPSender
 *
 * @package LaravelFCM\Sender
 */
abstract class HTTPSender
{
    /**
     * The client used to send messages.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The URL entry point.
     *
     * @var string
     */
    protected $url;

    /**
     * Initializes a new sender object.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string                     $url
     */
    public function __construct(ClientInterface $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }
}
