# Changelog

## 4.0.0

### Changes
- Removed travis support in favor of github actions.
- Rewritten package from scratch to be more strict.
- Raised php to ^8.1.
- Support for adapters added in favor of loader.
- Methods getting configuration values will no longer convert value, but throws an exception if incorrect type.

## Removed
- Removed support for specifying environment.

## 3.0.1

### Changed
- Changed to more correct naming from "Storage" to "Loader".

## 3.0.0

### Added
- Added Config->getString().
- Added Env::getAppName().
- Added Env::getAppDebug().

### Changed
- Config::env(), Config::envInt() and Config::envBool() moved to Env::class.
- Environment::getEnvironments() and Environment::isSupported() moved to Env::class.
- Config::appEnvironment() moved to Env::getAppEnvironment().

### Removed
- Removed requirement of vlucas/phpdotenv and made it a suggestion.
- Removed support for apps.
- Removed static approach. Config::class must be instantiated.
- Removed Config::all() in favor of Config->get({section}).
- Removed Config::repository() in favor of Config->get({section}).
- Options to modify configuration on the fly has been removed.

## 2.0.1

### Fixed
- Fixed wrong generation of config key.

## 2.0.0

### Changed
- Require php 7.2+
- Updated code to comply with Coding Standard.
- Removed corex/support in favor of other packages.

## 1.1.0

### Added
- Added Config::appPath().
- Added Config::unregisterApp().


## 1.0.2

### Changed
- .env files are no longer required. Environment defaults to production.


## 1.0.1

### Changed
- Upgraded symfony/finder to v4.x


## 1.0.0

### Added
- Initial release.
