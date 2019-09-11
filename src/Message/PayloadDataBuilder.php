<?php
declare(strict_types = 1);

namespace LaravelFCM\Message;

/**
 * Class PayloadDataBuilder.
 *
 * Official google documentation :
 *
 * @link http://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
 */
class PayloadDataBuilder
{
    /**
     * @internal
     *
     * @var array
     */
    protected $data;

    /**
     * add data to existing data.
     *
     * @param array $data
     *
     * @return self
     */
    public function addData(array $data): self
    {
        $this->data = $this->data ?: [];

        $this->data = array_merge($data, $this->data);

        return $this;
    }

    /**
     * erase data with new data.
     *
     * @param array $data
     *
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Remove all data.
     *
     * @return void
     */
    public function removeAllData(): void
    {
        $this->data = null;
    }

    /**
     * return data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Generate new PayloadData instance.
     *
     * @return PayloadData
     */
    public function build(): PayloadData
    {
        return new PayloadData($this);
    }
}
