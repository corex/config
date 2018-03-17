# CoRex Config

**_Versioning for this package follows http://semver.org/. Backwards compatibility might break on upgrade to major versions._**

This package provides config support for a project and has support for multiple apps. It is heavely inspired of Laravel's way of handling config-files.

For more detailed usage of dotenv support, you can get more information on package "vlucas/phpdotenv".

Configuration files live in a directory named "config" in the root directory of your project. It is possible to change this path via Config::registerApp().

Multiple config locations are supported through apps.

Support for setting config-environment using .env file. Config-environment is set on env-variable called "APP_ENV".
All config-files are read and while it is reading config-files, similar files for environment is loaded if found.
In other words - a config-file called "test.php" will be loaded and if a config-file for environment is set, it will be merged.
To set an environment config-file for "test.php", the name has to be "test.testing.php" where "testing" is the environment.
Valid environments can be found in Environment class.

```php
// Register path for myApp.
Config::registerApp('/my/app/path', 'myApp');

// Check if app is registered.
$isRegistered = Config::isAppRegistered('myApp');

// Get firstname of actor from global config.
$firstname = Config::get('actor.firstname');

// Get firstname of actor from myApp.
$firstname = Config::get('actor.firstname', null, 'myApp');

// Get number from global config.
$age = Config::getInt('actor.age');

// Get true/false from global config.
$isDead = Config::getBool('actor.isDead');

// Check if a key exists.
$hasKey = Config::has('myKey');

// Set value (not saved, only in-memory).
Config::set('myKey', 'some.value');

// Remove value.
Config::remove('myKey');

// Get complete config.
$all = Config::all();

// Get list of registered apps.
$apps = Config::apps();

// Get repository of default app with additional methods/features.
$repository = Config::repository();

// Get repository of 'myApp' app with additional methods/features.
$repository = Config::repository('myApp');

// Get value from .env file.
$value = Config::env('myKey', 'default.value');

// Get integer value from .env file.
$value = Config::envInt('myKey', 4);

// Get boolean value from .env file.
$value = Config::envBool('myKey', true);
```
