<?php

namespace CoRex\Config;

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
        try {
            $reflectionClass = new \ReflectionClass(__CLASS__);
            return array_values($reflectionClass->getConstants());
        } catch (\ReflectionException $e) {
            return [];
        }
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