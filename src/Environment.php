<?php

namespace CoRex\Config;

use CoRex\Support\Obj;

class Environment
{
    const LOCAL = 'local';
    const PRODUCTION = 'production';
    const TESTING = 'testing';

    /**
     * Get environments.
     *
     * @return array
     */
    public static function environments()
    {
        return Obj::getConstants(__CLASS__);
    }

    /**
     * Is supported.
     *
     * @param string $environment
     * @return boolean
     */
    public static function isSupported($environment)
    {
        return in_array($environment, self::environments());
    }
}