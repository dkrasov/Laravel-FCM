<?php
declare(strict_types = 1);

namespace LaravelFCM\Message;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class PayloadData
 *
 * @package LaravelFCM\Message
 */
class PayloadData implements Arrayable
{
    /**
     * @internal
     *
     * @var array
     */
    protected $data;

    /**
     * PayloadData constructor.
     *
     * @param PayloadDataBuilder $builder
     */
    public function __construct(PayloadDataBuilder $builder)
    {
        $this->data = $builder->getData();
    }

    /**
     * Transform payloadData to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
