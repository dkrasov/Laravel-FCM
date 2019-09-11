<?php
declare(strict_types = 1);

namespace LaravelFCM\Message;

use LaravelFCM\Message\Exceptions\InvalidOptionsException;
use ReflectionClass;

/**
 * Builder for creation of options used by FCM.
 *
 * Class OptionsBuilder
 *
 * @link http://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
 */
class OptionsBuilder
{
    /**
     * @internal
     *
     * @var string
     */
    protected $collapseKey;

    /**
     * @internal
     *
     * @var string
     */
    protected $priority;

    /**
     * @internal
     *
     * @var bool
     */
    protected $contentAvailable = false;

    /**
     * @internal
     *
     * @var bool
     */
    protected $mutableContent;

    /**
     * @internal
     *
     * @var bool
     */
    protected $delayWhileIdle = false;

    /**
     * @internal
     *
     * @var null|int
     */
    protected $timeToLive;

    /**
     * @internal
     *
     * @var string
     */
    protected $restrictedPackageName;

    /**
     * @internal
     *
     * @var bool
     */
    protected $dryRun = false;

    /**
     * This parameter identifies a group of messages
     * A maximum of 4 different collapse keys is allowed at any given time.
     *
     * @param string $collapseKey
     *
     * @return self
     */
    public function setCollapseKey(string $collapseKey): self
    {
        $this->collapseKey = $collapseKey;

        return $this;
    }

    /**
     * Sets the priority of the message. Valid values are "normal" and "high."
     * By default, messages are sent with normal priority.
     *
     * @param string $priority
     *
     * @return self
     * @throws InvalidOptionsException
     * @throws \ReflectionException
     */
    public function setPriority(string $priority): self
    {
        if (!OptionsPriorities::isValid($priority)) {
            throw new InvalidOptionsException(
                'The priority is not valid, please refer to the documentation or use the constants of the class "OptionsPriorities"'
            );
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * Support only Android and iOS.
     *
     * An inactive client app is awoken.
     * On iOS, use this field to represent content-available in the APNS payload.
     * On Android, data messages wake the app by default.
     * On Chrome, currently not supported.
     *
     * @param bool $contentAvailable
     *
     * @return self
     */
    public function setContentAvailable(bool $contentAvailable): self
    {
        $this->contentAvailable = $contentAvailable;

        return $this;
    }

    /**
     * Support iOS 10+
     *
     * When a notification is sent and this is set to true,
     * the content of the notification can be modified before it is displayed.
     *
     * @param string $isMutableContent
     *
     * @return self
     */
    public function setMutableContent(string $isMutableContent): self
    {
        $this->mutableContent = $isMutableContent;

        return $this;
    }

    /**
     * When this parameter is set to true, it indicates that the message should not be sent until the device becomes active.
     *
     * @param bool $delayWhileIdle
     *
     * @return self
     */
    public function setDelayWhileIdle(bool $delayWhileIdle): self
    {
        $this->delayWhileIdle = $delayWhileIdle;

        return $this;
    }

    /**
     * This parameter specifies how long the message should be kept in FCM storage if the device is offline.
     *
     * @param int $timeToLive (in second) min:0 max:2419200
     *
     * @return self
     * @throws InvalidOptionsException
     */
    public function setTimeToLive(int $timeToLive): self
    {
        if ($timeToLive < 0 || $timeToLive > 2419200) {
            throw new InvalidOptionsException("time to live must be between 0 and 2419200, current value is: {$timeToLive}");
        }

        $this->timeToLive = $timeToLive;

        return $this;
    }

    /**
     * This parameter specifies the package name of the application where the registration tokens must match in order to receive the message.
     *
     * @param string $restrictedPackageName
     *
     * @return self
     */
    public function setRestrictedPackageName(string $restrictedPackageName): self
    {
        $this->restrictedPackageName = $restrictedPackageName;

        return $this;
    }

    /**
     * This parameter, when set to true, allows developers to test a request without actually sending a message.
     * It should only be used for the development.
     *
     * @param bool $isDryRun
     *
     * @return self
     */
    public function setDryRun(bool $isDryRun): self
    {
        $this->dryRun = $isDryRun;

        return $this;
    }

    /**
     * Get the collapseKey.
     *
     * @return null|string
     */
    public function getCollapseKey(): ?string
    {
        return $this->collapseKey;
    }

    /**
     * Get the priority.
     *
     * @return null|string
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * is content available.
     *
     * @return bool
     */
    public function isContentAvailable(): bool
    {
        return $this->contentAvailable;
    }

    /**
     * is mutable content
     *
     * @return bool|null
     */
    public function isMutableContent(): ?bool
    {
        return $this->mutableContent;
    }

    /**
     * is delay white idle.
     *
     * @return bool
     */
    public function isDelayWhileIdle(): bool
    {
        return $this->delayWhileIdle;
    }

    /**
     * get time to live.
     *
     * @return null|int
     */
    public function getTimeToLive(): ?int
    {
        return $this->timeToLive;
    }

    /**
     * get restricted package name.
     *
     * @return null|string
     */
    public function getRestrictedPackageName(): ?string
    {
        return $this->restrictedPackageName;
    }

    /**
     * is dry run.
     *
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * build an instance of Options.
     *
     * @return Options
     */
    public function build(): Options
    {
        return new Options($this);
    }
}

/**
 * Class OptionsPriorities.
 */
final class OptionsPriorities
{
    /**
     * @const high priority : iOS, these correspond to APNs priorities 10.
     */
    public const high = 'high';

    /**
     * @const normal priority : iOS, these correspond to APNs priorities 5
     */
    public const normal = 'normal';

    /**
     * priorities available in FCM.
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getPriorities(): array
    {
        $class = new ReflectionClass(__CLASS__);

        return $class->getConstants();
    }

    /**
     * Check if this priority is supported by fcm.
     *
     * @param $priority
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function isValid($priority): bool
    {
        return in_array($priority, static::getPriorities(), true);
    }
}
