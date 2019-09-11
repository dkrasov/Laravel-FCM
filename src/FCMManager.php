<?php
declare(strict_types = 1);

namespace LaravelFCM;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;

/**
 * Class FCMManager
 *
 * @package LaravelFCM
 */
class FCMManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return (string)config('fcm.driver');
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function createHttpDriver(): Client
    {
        return new Client(['timeout' => config('fcm.http.timeout')]);
    }
}
