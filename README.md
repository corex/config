# Config Component

![License](https://img.shields.io/packagist/l/corex/config.svg)
![Build Status](https://travis-ci.org/corex/config.svg?branch=master)
![codecov](https://codecov.io/gh/corex/config/branch/master/graph/badge.svg)

This package has been rewritten from scratch. The purpose was to modernize
the code and add support for storages. And at the same time keep it as simple
as possible. Breaking changes can be found in CHANGELOG.

## Storages

Previous versions of this package only supported configurations living in {root}/config directory through php files.
This version now support storages and requires you to setup a storage prior to instantiating configuration class.

```php
$storage = new PhpStorage('/my/path/to/storage');
$config = new Config($storage);
```

You can easily implement another storage i.e. Yaml, Database, etc...., by implementing StorageInterface::class.

## Fetching values

Following methods exists to help fetch values.

- has() which check if a value is present.
- get() to get value. No type-conversion.
- getString() will always return a string.
- getInt() will always return an int.
- getBool() will always return a bool. Following values will be considered true: [1, true, '1', 'true', 'yes', 'on'].

Example of config file "database.php".
```php
return [
    'main' => [
        'host' => 'myhost',
        'primary' => true
    ]
];
```

Example of fetching a value with default value.

```php
$host = $config->get('database.main.host', 'localhost');
$isPrimary = $config->getBool('database.main.primary');
```

## Environment variables

It is possible to override configuration values via environment variables.
If you have a key i.e. "database.main.host" you can override it by setting an environment variable
called "DATABASE_MAIN_HOST".

Hint:
It is recommended to use vlucas/phpdotenv which loads environment variables from `.env`.

A template `.env.example` exists for you to copy and fill in values of your own.

App-specific environment variables APP_NAME, APP_ENV and APP_DEBUG are supported through Env::class.

- APP_NAME can be fetched through Env::getAppName().
- APP_ENV can be fetched through Env::getAppEnvironment(). "local", "testing" and "production" are supported. Env::getAppEnvironment() defaults to Env::PRODUCTION.
- APP_DEBUG can be fetched through Env::getAppDebug(). Following values will be considered true: [1, true, '1', 'true', 'yes', 'on'].
