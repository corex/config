<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Data\Value;

final class ServerAdapter extends AbstractAdapter
{
    /** @var array<string, bool|float|int|string|null> */
    private array $serverKeyValues;

    public function __construct()
    {
        $this->serverKeyValues = $_SERVER; // phpcs:ignore
    }

    /**
     * @inheritDoc
     */
    public function getValue(KeyInterface $key): Value
    {
        $serverKey = $key->getShoutCase();

        return new Value(
            array_key_exists($serverKey, $this->serverKeyValues) ? $this : null,
            $key,
            $this->serverKeyValues[$serverKey] ?? null
        );
    }
}