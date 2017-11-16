<?php

namespace CoRex\Config;

use Dotenv\Dotenv;

class Config
{
    private static $repositories;

    /**
     * Has.
     *
     * @param string $key
     * @param string $app Default null which means default app '*'.
     * @return boolean
     */
    public static function has($key, $app = null)
    {
        return self::repository($app)->has($key);
    }

    /**
     * Get.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @param string $app Default null which means default app '*'.
     * @return mixed
     */
    public static function get($key, $default = null, $app = null)
    {
        return self::repository($app)->get($key, $default);
    }

    /**
     * Get many.
     *
     * @param array $keys
     * @param string $app Default null which means default app '*'.
     * @return array
     */
    public static function getMany(array $keys, $app = null)
    {
        return self::repository($app)->getMany($keys);
    }

    /**
     * All.
     *
     * @param string $app Default null which means default app '*'.
     * @return array
     */
    public static function all($app = null)
    {
        return self::repository($app)->all();
    }

    /**
     * Get apps.
     *
     * @return array
     */
    public static function apps()
    {
        self::initialize();
        return array_keys(self::$repositories);
    }

    /**
     * Register app.
     *
     * @param string $path
     * @param string $app Default null which means default app '*'.
     * @throws ConfigException
     */
    public static function registerApp($path, $app = null)
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }
        if (isset(self::$repositories[$app])) {
            throw new ConfigException('Application ' . $app . ' already registered.');
        }
        $path = rtrim($path, '/');
        if (!is_dir($path)) {
            throw new ConfigException('Path ' . $path . ' does not exist.');
        }
        self::$repositories[$app] = new Repository($path);
    }

    /**
     * Get repository.
     *
     * @param string $app Default null which means default app '*'.
     *
     * @return Repository
     * @throws ConfigException
     */
    public static function repository($app = null)
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }

        // Autoload default "config" in project root.
        if ($app == '*' && !isset(self::$repositories[$app])) {
            $path = Path::root(['config']);
            if (!is_dir($path)) {
                throw new ConfigException('Path ' . $path . ' does not exist.');
            }
            self::$repositories[$app] = new Repository($path);
        }

        if (!isset(self::$repositories[$app])) {
            throw new ConfigException('Application ' . $app . ' not registered.');
        }
        return self::$repositories[$app];
    }

    /**
     * Env.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     */
    public static function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }

    /**
     * Env int.
     *
     * @param string $key
     * @param integer $default Default 0.
     * @return integer
     */
    public static function envInt($key, $default = 0)
    {
        return intval(self::env($key, $default));
    }

    /**
     * Env bool.
     *
     * @param string $key
     * @param boolean $default Default false.
     * @return boolean
     */
    public static function envBool($key, $default = false)
    {
        $value = self::env($key, $default);
        if (is_string($value)) {
            $value = strtolower($value);
        }
        return in_array($value, [1, true, '1', 'true', 'yes']);
    }

    /**
     * Initialize.
     */
    private static function initialize()
    {
        // Load dotenv.
        $dotenv = new Dotenv(Path::root());
        $dotenv->load();

        // Initialize repositories.
        if (!is_array(self::$repositories)) {
            self::$repositories = [];
        }
    }
}