# Config Component

![license](https://img.shields.io/github/license/corex/config?label=license)
![build](https://github.com/corex/config/workflows/build/badge.svg?branch=master)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/corex/d409d31a9138bc37c905b4b4727bebe1/raw/test-coverage__master.json)](https://github.com/corex/config/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/corex/d409d31a9138bc37c905b4b4727bebe1/raw/phpstan-level__master.json)](https://github.com/corex/config/actions)

> **Breaking changes** - this package has been rewritten from scratch to be more strict and flexible to use. Adapters are supported in favor of loaders. Breaking changes can be found in CHANGELOG.

Getting configuration values works by creating instance of Config::class and use methods to get values. Multiple adapters are supported.

Example:
```php
$adapter = new ArrayAdapter([
    'actor' => [
        'name' => 'James Bond',
    ],
]);

$config = new Config([$adapter]);

$actorName = $config->getString('actor.name');
```

## ConfigFactory

A ConfigFactory exists to make it easier to create instances of Config::class.

Example:
```php
$config = (new ConfigFactory())
    ->createWithServerAndEnvAndProjectConfigArrayFileAdapter();
```

The above example exposes 3 adapters ServerAdapter, EnvAdapter and ProjectConfigArrayFileAdapter.

From above example, when getting value, the process is following.
- ServerAdapter is checked for key. If found, value is returned.
- EnvAdapter is checked for key. If found, value is returned.
- ProjectConfigArrayFileAdapter is checked for key. If found, value is returned.

Based on various methods to get values, a null is returned or an exception is thrown.

More methods exists, but in the situation where they does not fit in, instantiate Config::class with your own order of adapters.


## Keys

Every key must be specified as dot notation e.g. "actor.name".

> "_" and "-" will not be treated as separators.

> When using ServerAdapter and EnvAdapter, the key will be converted to shoutcase e.g. ACTOR_NAME. This makes it easy to override values in e.g. cloud environments.

On key object, multiple methods exists to get key in various formats. Use `custom()` to build your own.


## Config

Various methods exists on config-class for getting values in correct format. There exists methods for getting specific type with or without null eg. "getString()" or "getStringOrNull()" . Following types are supported: string, int, bool translated-bool, double, array, list.

For all type methods, value from adapters will be checked if they are correct type, otherwise an exception is thrown.

> "translated-bool" translates/converts following values to boolean.
>
> Values for true : ['true', true, 'yes', 'on', 1, '1'].
>
> Values for false : ['false', false, 'no', 'off', 0, '0'].

> "list" means an array where keys are numeric keys 0..n.


## Adapters

It is possible to write your own adapter by extending AbstractAdapter or implementing AdapterInterface.

Following standard adapters expose arrays as the basis for configuration values.

**ArrayAdapter**

Serve simple array.

```php
$adapter = new ArrayAdapter([
    'actor' => [
        'name' => 'James Bond',
    ],
]);
```


**EnvAdapter**

Serve $_ENV global array.

```php
$adapter = new EnvAdapter();
```


**ServerAdapter**

Serve $_SERVER global array.

```php
$adapter = new ServerAdapter();
```


**ArrayFileAdapter**

Serve php array files outside project root.

```php
$adapter = new ArrayFileAdapter(new Filesystem(), '/config-dir-outside-project-root');
```


**ProjectPathArrayFileAdapter - Serve**

Serve php array files in project root from relative directory.

```php
$adapter = new ProjectPathArrayFileAdapter(new Filesystem(), 'my-config-dir');
```


**ProjectConfigArrayFileAdapter**

Serve php array files in project root from relative directory called "config".

```php
$adapter = new ProjectConfigArrayFileAdapter(new Filesystem());
```


### Array files.

Example of an array-file.

Name of file "**bond.php**".
```php
<?php

declare(strict_types=1);

return [
    'actor1' => [
        'firstname' => 'Roger',
        'lastname' => 'Moore'
    ],
    'actor3' => [
        'firstname' => 'Daniel',
        'lastname' => 'Craig'
    ]
];
```

These type of files can be loaded via ArrayFileAdapter, ProjectPathArrayFileAdapter and ProjectConfigArrayFileAdapter.

Example of a config-key: "bond.actor1.firstname" which will return "Roger".

First section of key "bond" indicates the section and on these adapters the filename.
