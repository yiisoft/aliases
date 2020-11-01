<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Aliases</h1>
    <br>
</p>

The package aim is to store path aliases, i.e. short name representing a long path (a file path, a URL, etc.).
Path alias value may have another value as its part. For example, `@vendor` may store path to `vendor` directory
while `@bin` may store `@vendor/bin`. 

[![Latest Stable Version](https://poser.pugx.org/yiisoft/aliases/v/stable.png)](https://packagist.org/packages/yiisoft/aliases)
[![Total Downloads](https://poser.pugx.org/yiisoft/aliases/downloads.png)](https://packagist.org/packages/yiisoft/aliases)
[![Build Status](https://github.com/yiisoft/aliases/workflows/build/badge.svg)](https://github.com/yiisoft/aliases/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/aliases/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/aliases/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/aliases/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/aliases/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https://badge-api.stryker-mutator.io/github.com/yiisoft/aliases/master)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/aliases/master)
[![static analysis](https://github.com/yiisoft/aliases/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/aliases/actions?query=workflow%3A%22static+analysis%22)

## General usage

A path alias must start with the character '@' so that it can be easily differentiated from non-alias paths.

```php
use Yiisoft\Aliases\Aliases;

$aliases = new Aliases([
    '@root' => __DIR__,
]);
$aliases->set('@vendor', '@root/vendor');
$aliases->set('@bin', '@vendor/bin');

echo $aliases->get('@bin/phpunit');
```

The code about would output "/path/to/vendor/bit/phpunit".

Note that `set()` method does not check if the given path exists or not. All it does is to associate the alias with
the path.

The path could be:

- a directory or a file path (e.g. `/tmp`, `/tmp/main.txt`)
- a URL (e.g. `http://www.yiiframework.com`)
- a path alias (e.g. `@yii/base`). It will be resolved on {@see get()} call.

Any trailing `/` and `\` characters in the given path will be trimmed.

### Alias priorities

In case multiple aliases are registered with same root (prefix), then the most specific has precedence:

```php
use Yiisoft\Aliases\Aliases;

$aliases = new Aliases([
    '@vendor' => __DIR__ . '/vendor',
    '@vendor/test' => '/special/location'    
]);
echo $aliases->get('@vendor/test');
```

That would output `/special/location` since `@vendor/test` is more specific match than `@vendor`. 

### Alias removal

If you need to remove alias runtime:

```php
use Yiisoft\Aliases\Aliases;

$aliases = new Aliases([
    '@root' => __DIR__,
]);
$aliases->remove('@root');
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Aliases is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
