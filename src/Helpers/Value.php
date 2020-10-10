<?php

declare(strict_types=1);

namespace CoRex\Config\Helpers;

class Value
{
    /**
     * Is true.
     *
     * Following values (1, true, '1', 'true', 'yes', 'on')
     * will automatically be true. All other values are false.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isTrue($value): bool
    {
        if (is_string($value)) {
            $value = strtolower($value);
        }

        return in_array($value, [1, true, '1', 'true', 'yes', 'on'], true);
    }
}