<?php

declare(strict_types=1);

namespace CoRex\Config;

use CoRex\Config\Exceptions\ConfigException;
use CoRex\Filesystem\File;
use Dotenv\Dotenv;

class Config
{
    /** @var string */
    private static $path;

    /** @var bool */
    private static $isLoaded;

    /** @var Repository[] */
    private static $repositories;

    /** @var Dotenv */
    private static $dotenv;

    /**
     * Has.
     *
     * @param string $key
     * @param string $app Default null which means default app '*'.
     * @return bool
     * @throws ConfigException
     */
    public static function has(string $key, ?string $app = null): bool
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
    public static function get(string $key, $defaultValue = null, ?string $app = null)
    {
        return self::repository($app)->get($key, $defaultValue);
    }

    /**
     * Get integer.
     *
     * @param string $key
     * @param int $defaultValue Default 0.
     * @param string $app Default null which means default app '*'.
     * @return int
     * @throws ConfigException
     */
    public static function getInt(string $key, int $defaultValue = 0, ?string $app = null): int
    {
        return self::repository($app)->getInt($key, $defaultValue);
    }

    /**
     * Get boolean.
     *
     * @param string $key
     * @param bool $defaultValue Default false.
     * @param string $app Default null which means default app '*'.
     * @return bool
     * @throws ConfigException
     */
    public static function getBool(string $key, bool $defaultValue = false, ?string $app = null): bool
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
    public static function set(string $key, $value, ?string $app = null): void
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
    public static function remove(string $key, ?string $app = null): void
    {
        self::repository($app)->remove($key);
    }

    /**
     * All.
     *
     * @param string $app Default null which means default app '*'.
     * @return mixed[]
     * @throws ConfigException
     */
    public static function all(?string $app = null): array
    {
        return self::repository($app)->all();
    }

    /**
     * Get apps.
     *
     * @return string[]
     */
    public static function apps(): array
    {
        self::initialize();
        $apps = [];
        foreach (self::$repositories as $name => $repository) {
            $apps[] = $name;
        }
        return $apps;
    }

    /**
     * Register app.
     *
     * @param string $path
     * @param string $app Default null which means default app '*'.
     * @throws ConfigException
     */
    public static function registerApp(string $path, ?string $app = null): void
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
     * @return bool
     */
    public static function isAppRegistered(?string $app = null): bool
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
    public static function unregisterApp(?string $app = null): void
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
     * @return Repository
     * @throws ConfigException
     */
    public static function repository(?string $app = null): Repository
    {
        self::initialize();
        if ($app === null) {
            $app = '*';
        }

        // Autoload default "config" in project root.
        if ($app === '*' && !isset(self::$repositories[$app])) {
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
    public static function env(string $key, $default = null)
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
     * @param int $default Default 0.
     * @return int
     */
    public static function envInt(string $key, int $default = 0): int
    {
        return intval(self::env($key, $default));
    }

    /**
     * Env bool.
     *
     * @param string $key
     * @param bool $default Default false.
     * @return bool
     */
    public static function envBool(string $key, bool $default = false): bool
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
    public static function appEnvironment(): string
    {
        return self::env('APP_ENV', Environment::PRODUCTION);
    }

    /**
     * App path.
     *
     * @param string $app Default null which means default app '*'.
     * @return string
     */
    public static function appPath(?string $app = null): ?string
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
    public static function initialize(?string $path = null): void
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