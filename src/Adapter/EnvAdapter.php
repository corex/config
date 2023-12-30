<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Data\Value;

final class EnvAdapter extends AbstractAdapter
{
    /** @var array<int|string, bool|float|int|string|null> */
    private array $envValues;

    public function __construct()
    {
        $this->envValues = $_ENV; // phpcs:ignore
    }

    /**
     * @inheritDoc
     */
    public function getValue(KeyInterface $key): Value
    {
        $serverKey = $key->getShoutCase();

        return new Value(
            array_key_exists($serverKey, $this->envValues) ? $this : null,
            $key,
            $this->envValues[$serverKey] ?? null
        );
    }
}