<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Aliases</h1>
    <br>
</p>

The package aim is to store named values where one value may have another values as its part. For example, `@vendor` may
store path to `vendor` directory while `@bin` may store `@vendor/bin`. 

[![Latest Stable Version](https://poser.pugx.org/yiisoft/aliases/v/stable.png)](https://packagist.org/packages/yiisoft/aliases)
[![Total Downloads](https://poser.pugx.org/yiisoft/aliases/downloads.png)](https://packagist.org/packages/yiisoft/aliases)
[![Build Status](https://travis-ci.com/yiisoft/aliases.svg?branch=master)](https://travis-ci.com/yiisoft/aliases)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/aliases/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/aliases/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/aliases/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/aliases/?branch=master)

## General usage

```php
$aliases = new Aliases();
$aliases->set('@root', __DIR__);
$aliases->set('@vendor', '@root/vendor');
$aliases->set('@bin', '@vendor/bin');

echo $aliases->get('@bin');        
```
