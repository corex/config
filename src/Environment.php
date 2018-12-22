<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Helpers\Obj;

class Environment
{
    public const LOCAL = 'local';
    public const PRODUCTION = 'production';
    public const TESTING = 'testing';

    /**
     * Get environments.
     *
     * @return string[]
     */
    public static function environments(): array
    {
        return Obj::getConstants(__CLASS__);
    }

    /**
     * Is supported.
     *
     * @param string $environment
     * @return bool
     */
    public static function isSupported(string $environment): bool
    {
        return in_array($environment, self::environments());
    }
}