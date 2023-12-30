<?php

declare(strict_types=1);

namespace CoRex\Config\Data;

use CoRex\Config\Adapter\AdapterInterface;
use CoRex\Config\Key\KeyInterface;

final class Value
{
    private ?AdapterInterface $adapter;
    private KeyInterface $key;

    /** @var array<int|string, mixed> */
    private array|bool|float|int|string|null $value;

    /**
     * @param array<int|string, mixed> $value
     */
    public function __construct(?AdapterInterface $adapter, KeyInterface $key, array|bool|float|int|string|null $value)
    {
        $this->adapter = $adapter;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get adapter or null if key not found.
     *
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * Get key.
     *
     * @return KeyInterface
     */
    public function getKey(): KeyInterface
    {
        return $this->key;
    }

    /**
     * Has key.
     *
     * @return bool
     */
    public function hasKey(): bool
    {
        return $this->adapter !== null;
    }

    /**
     * Get value.
     *
     * @return array<int|string, mixed>|bool|float|int|string|null
     */
    public function getValue(): array|bool|float|int|string|null
    {
        return $this->value;
    }
}