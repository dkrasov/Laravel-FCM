<?php
declare(strict_types = 1);

namespace LaravelFCM\Message;

/**
 * Class PayloadNotificationBuilder.
 *
 * Official google documentation :
 *
 * @link http://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
 */
class PayloadNotificationBuilder
{
    /**
     * @internal
     *
     * @var null|string
     */
    protected $title;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $body;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $icon;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $sound;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $channelId;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $badge;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $tag;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $color;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $clickAction;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $bodyLocationKey;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $bodyLocationArgs;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $titleLocationKey;

    /**
     * @internal
     *
     * @var null|string
     */
    protected $titleLocationArgs;

    /**
     * Title must be present on android notification and ios (watch) notification.
     *
     * @param string $title
     */
    public function __construct($title = null)
    {
        $this->title = $title;
    }

    /**
     * Indicates notification title. This field is not visible on iOS phones and tablets.
     * but it is required for android.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Indicates notification body text.
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set a channel ID for android API >= 26.
     *
     * @param string $channelId
     *
     * @return self
     */
    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Supported Android
     * Indicates notification icon. example : Sets value to myicon for drawable resource myicon.
     *
     * @param string $icon
     *
     * @return self
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Indicates a sound to play when the device receives a notification.
     * Supports default or the filename of a sound resource bundled in the app.
     *
     * @param string $sound
     *
     * @return self
     */
    public function setSound(string $sound): self
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Supported Ios.
     *
     * Indicates the badge on the client app home icon.
     *
     * @param string $badge
     *
     * @return self
     */
    public function setBadge(string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Supported Android.
     *
     * Indicates whether each notification results in a new entry in the notification drawer on Android.
     * If not set, each request creates a new notification.
     * If set, and a notification with the same tag is already being shown, the new notification replaces the existing one in the notification drawer.
     *
     * @param string $tag
     *
     * @return self
     */
    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Supported Android.
     *
     * Indicates color of the icon, expressed in #rrggbb format
     *
     * @param string $color
     *
     * @return self
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Indicates the action associated with a user click on the notification.
     *
     * @param string $action
     *
     * @return self
     */
    public function setClickAction(string $action): self
    {
        $this->clickAction = $action;

        return $this;
    }

    /**
     * Indicates the key to the title string for localization.
     *
     * @param string $titleKey
     *
     * @return self
     */
    public function setTitleLocationKey(string $titleKey): self
    {
        $this->titleLocationKey = $titleKey;

        return $this;
    }

    /**
     * Indicates the string value to replace format specifiers in the title string for localization.
     *
     * @param mixed $titleArgs
     *
     * @return self
     */
    public function setTitleLocationArgs($titleArgs): self
    {
        $this->titleLocationArgs = $titleArgs;

        return $this;
    }

    /**
     * Indicates the key to the body string for localization.
     *
     * @param string $bodyKey
     *
     * @return self
     */
    public function setBodyLocationKey(string $bodyKey): self
    {
        $this->bodyLocationKey = $bodyKey;

        return $this;
    }

    /**
     * Indicates the string value to replace format specifiers in the body string for localization.
     *
     * @param mixed $bodyArgs
     *
     * @return self
     */
    public function setBodyLocationArgs($bodyArgs): self
    {
        $this->bodyLocationArgs = $bodyArgs;

        return $this;
    }

    /**
     * Get title.
     *
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get body.
     *
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Get channel id for android api >= 26
     *
     * @return null|string
     */
    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    /**
     * Get Icon.
     *
     * @return null|string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get Sound.
     *
     * @return null|string
     */
    public function getSound(): ?string
    {
        return $this->sound;
    }

    /**
     * Get Badge.
     *
     * @return null|string
     */
    public function getBadge(): ?string
    {
        return $this->badge;
    }

    /**
     * Get Tag.
     *
     * @return null|string
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * Get Color.
     *
     * @return null|string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Get ClickAction.
     *
     * @return null|string
     */
    public function getClickAction(): ?string
    {
        return $this->clickAction;
    }

    /**
     * Get BodyLocationKey.
     *
     * @return null|string
     */
    public function getBodyLocationKey(): ?string
    {
        return $this->bodyLocationKey;
    }

    /**
     * Get BodyLocationArgs.
     *
     * @return null|string|array
     */
    public function getBodyLocationArgs()
    {
        return $this->bodyLocationArgs;
    }

    /**
     * Get TitleLocationKey.
     *
     * @return string
     */
    public function getTitleLocationKey(): ?string
    {
        return $this->titleLocationKey;
    }

    /**
     * GetTitleLocationArgs.
     *
     * @return null|string|array
     */
    public function getTitleLocationArgs()
    {
        return $this->titleLocationArgs;
    }

    /**
     * Build an PayloadNotification.
     *
     * @return PayloadNotification
     */
    public function build(): PayloadNotification
    {
        return new PayloadNotification($this);
    }
}
