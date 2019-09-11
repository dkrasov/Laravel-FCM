<?php
declare(strict_types = 1);

namespace LaravelFCM\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FCM
 *
 * @package LaravelFCM\Facades
 */
class FCM extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fcm.sender';
    }
}
