<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Identifier\AdapterIdentifierInterface;
use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Data\Value;

interface AdapterInterface
{
    /**
     * Get identifier to ensure unique adapters.
     *
     * Note: Remember to add arguments from constructor to create unique identifier.
     *
     * @return AdapterIdentifierInterface
     */
    public function getIdentifier(): AdapterIdentifierInterface;

    /**
     * Get config (section) array.
     *
     * @param KeyInterface $key
     * @return Value
     */
    public function getValue(KeyInterface $key): Value;
}