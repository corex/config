# CoRex Config

**_Versioning for this package follows http://semver.org/. Backwards compatibility might break on upgrade to major versions._**

This package provides config support for a project and is primarily based on illuminate/config and vlucas/phpdotenv + support for multiple apps. It is heavely inspired of Laravel's way of handling config-files.

For more detailed usage of dotenv support, you can get more information on package "vlucas/phpdotenv".

Configuration files live in a directory named "config" in the root directory of your project. It is possible to change this path via Config::registerApp().

Multiple locations are supported through apps (repositories).

Support for multiple levels (sub-directories) of config-files.

```php
// Register path for myApp.
Config::registerApp('/my/app/path', 'myApp');

// Get firstname of actor from global access.
$firstname = Config::get('actor.firstname');

// Get firstname of actor from myApp.
$firstname = Config::get('actor.firstname', null, 'myApp');

// Check if a key exists.
$hasKey = Config::has('myKey');

// Get list of apps.
$apps = Config::apps();

// Get repository of app with additional methods/features (Package illuminate/config).
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
