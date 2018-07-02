<?php

namespace CoRex\Config;

use CoRex\Support\System\File;
use CoRex\Support\System\Path;
use Dotenv\Dotenv;

class Config
{
    private static $path;
    private static $isLoaded;
    private static $repositories;
    private static $dotenv;

    /**
     * Has.
     *
     * @param string $key
     * @param string $app Default null which means default app '*'.
     * @return boolean
     * @throws ConfigException
     */
    public static function has($key, $app = null)
    {
        return self::repository($app)->has($key);
    }

    /**
     * Get.
     *
     * @param string $key
     * @param mixed $defaultValue Default null.
     * @param string $app Default null which means default app '*'.
     * @return mixed
     * @throws ConfigException
     */
    public static function get($key, $defaultValue = null, $app = null)
    {
        return self::repository($app)->get($key, $defaultValue);
    }

    /**
     * Get integer.
     *
     * @param string $key
     * @param integer $defaultValue Default 0.
     * @param string $app Default null which means default app '*'.
     * @return integer
     * @throws ConfigException
     */
    public static function getInt($key, $defaultValue = 0, $app = null)
    {
        return self::repository($app)->getInt($key, $defaultValue);
    }

    /**
     * Get boolean.
     *
     * @param string $key
     * @param boolean $defaultValue Default false.
     * @param string $app Default null which means default app '*'.
     * @return boolean
     * @throws ConfigException
     */
    public static function getBool($key, $defaultValue = false, $app = null)
    {
        return self::repository($app)->getBool($key, $defaultValue);
    }

    /**
     * Set.
     *
     * @param string $key
     * @param mixed $value
     * @param string $app Default null which means default app '*'.
     * @throws ConfigException
     */
    public static function set($key, $value, $app = null)
    {
        self::repository($app)->set($key, $value);
    }

    /**
     * Remove key.
     *
     * @param string $key
     * @param string $app Default null which means default app '*'.
     * @throws ConfigException
     */
    public static function remove($key, $app = null)
    {
        self::repository($app)->remove($key);
    }

    /**
     * All.
     *
     * @param string $app Default null which means default app '*'.
     * @return array
     * @throws ConfigException
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
     * Is app registered.
     *
     * @param string $app Default null which means default app '*'.
     * @return boolean
     */
    public static function isAppRegistered($app = null)
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }
        return isset(self::$repositories[$app]);
    }

    /**
     * Unregister app.
     *
     * @param string $app Default null which means default app '*'.
     */
    public static function unregisterApp($app = null)
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }
        if (self::isAppRegistered($app)) {
            unset(self::$repositories[$app]);
        }
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
            $path = Path::root('config');
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
        self::initialize();
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
     * App environment (default local).
     *
     * @return string
     */
    public static function appEnvironment()
    {
        return self::env('APP_ENV', Environment::PRODUCTION);
    }

    /**
     * App path.
     *
     * @param string $app Default null which means default app '*'.
     * @return string
     */
    public static function appPath($app = null)
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }
        if (isset(self::$repositories[$app])) {
            return call_user_func([self::$repositories[$app], 'getPath']);
        }
        return null;
    }

    /**
     * Initialize.
     *
     * @param string $path Default null which means root of project.
     */
    public static function initialize($path = null)
    {
        if (self::$isLoaded === true) {
            return;
        }

        // Load dotenv.
        self::$path = $path;
        if (self::$path === null) {
            self::$path = Path::root();
        }
        if (File::exist(self::$path . '/.env')) {
            self::$dotenv = new Dotenv(self::$path);
            self::$dotenv->load();
        }

        // Initialize repositories.
        if (!is_array(self::$repositories)) {
            self::$repositories = [];
        }

        self::$isLoaded = true;
    }
}