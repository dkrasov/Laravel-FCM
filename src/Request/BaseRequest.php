<?php
declare(strict_types = 1);

namespace LaravelFCM\Request;

use function config;

/**
 * Class BaseRequest
 *
 * @package LaravelFCM\Request
 */
abstract class BaseRequest
{
    /**
     * @internal
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @internal
     *
     * @var array
     */
    protected $config;

    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        $this->config = config('fcm.http', []);
    }

    /**
     * Build the header for the request.
     *
     * @return array
     */
    protected function buildRequestHeader(): array
    {
        return [
            'Authorization' => 'key='.$this->config['server_key'],
            'Content-Type'  => 'application/json',
            'project_id'    => $this->config['sender_id'],
        ];
    }

    /**
     * Build the body of the request.
     *
     * @return mixed
     */
    abstract protected function buildBody();

    /**
     * Return the request in array form.
     *
     * @return array
     */
    public function build(): array
    {
        return [
            'headers' => $this->buildRequestHeader(),
            'json'    => $this->buildBody(),
        ];
    }
}
