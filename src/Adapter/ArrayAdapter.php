<?php

declare(strict_types=1);

namespace CoRex\Config\Adapter;

use CoRex\Config\Data\Data;
use CoRex\Config\Key\KeyInterface;
use CoRex\Config\Data\Value;

class ArrayAdapter extends AbstractAdapter
{
    /** @var array<int|string, mixed> */
    private array $data;

    /**
     * @param array<int|string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getValue(KeyInterface $key): Value
    {
        $configValue = Data::getFromArray($this->data, $key->getParts(false));

        return new Value(
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $this : null,
            $key,
            $configValue !== Data::NO_VALUE_FOUND_CODE ? $configValue : null
        );
    }
}