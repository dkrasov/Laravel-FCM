<?php
declare(strict_types = 1);

namespace LaravelFCM\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FCMGroup
 *
 * @package LaravelFCM\Facades
 */
class FCMGroup extends Facade
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
        return 'fcm.group';
    }
}
