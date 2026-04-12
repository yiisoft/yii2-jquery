Installation
============

Requirements
------------

* [PHP](https://www.php.net/downloads) `8.3` or higher.
* [Composer](https://getcomposer.org/download/) for dependency management.
* [`yiisoft/yii2`](https://github.com/yiisoft/yii2) `22.x`.


Installing via Composer
-----------------------

The preferred way to install this extension is through [Composer](https://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiisoft/yii2-jquery
```

or add

```json
{
    "require": {
        "yiisoft/yii2-jquery": "~1.0.0"
    }
}
```

to the `require` section of your `composer.json` file, then run:

```
composer update
```


Next steps
----------

Once the installation is complete, continue with:

* [Configuration](configuration.md) — register the bootstrap class and review what it configures.
* [Basic usage and examples](usage.md) — override a single validator or widget client script.
* [Testing](testing.md) — local quality and test tooling.
