<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://www.yiiframework.com/image/yii_logo_dark.svg">
        <source media="(prefers-color-scheme: light)" srcset="https://www.yiiframework.com/image/yii_logo_light.svg">
        <img src="https://www.yiiframework.com/image/yii_logo_light.svg" alt="Yii Framework" height="100px">
    </picture>
    <h1 align="center">jQuery Integration Extension for Yii2</h1>
    <br>
</p>

This extension provides the optional jQuery integration layer for [Yii Framework 2.0](https://www.yiiframework.com)
applications. It supplies jQuery-backed asset bundles, client-side validation scripts, and widget client scripts for
every core validator, grid component, and widget that supports the client-script strategy pattern.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-jquery.svg?style=for-the-badge&label=Stable&logo=packagist)](https://packagist.org/packages/yiisoft/yii2-jquery)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-jquery.svg?style=for-the-badge&label=Downloads)](https://packagist.org/packages/yiisoft/yii2-jquery)
[![build](https://img.shields.io/github/actions/workflow/status/yiisoft/yii2-jquery/build.yml?style=for-the-badge&logo=github&label=Build)](https://github.com/yiisoft/yii2-jquery/actions?query=workflow%3Abuild)
[![codecov](https://img.shields.io/codecov/c/github/yiisoft/yii2-jquery.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Codecov)](https://codecov.io/gh/yiisoft/yii2-jquery)
[![Static Analysis](https://img.shields.io/github/actions/workflow/status/yiisoft/yii2-jquery/static.yml?style=for-the-badge&label=Static)](https://github.com/yiisoft/yii2-jquery/actions/workflows/static.yml)


Installation
------------

> [!IMPORTANT]
> - The minimum required [PHP](https://www.php.net/) version is PHP `8.3`.
> - Requires [`yiisoft/yii2`](https://github.com/yiisoft/yii2) `22.x`.

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
composer require --prefer-dist yiisoft/yii2-jquery:~1.0.0
```

or add

```
"yiisoft/yii2-jquery": "~1.0.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, register the bootstrap class in your application configuration:

```php
return [
    'bootstrap' => [
        \yii\jquery\Bootstrap::class,
    ],
    // ...
];
```

The `Bootstrap` class configures the DI container with jQuery-based `$clientScript` defaults for every core validator,
grid component, and widget that supports the client-script strategy pattern. No further configuration is required.


Overriding a single validator
-----

You can override the client-script implementation on a per-rule basis by passing the `clientScript` key in the rule
definition:

```php
public function rules(): array
{
    return [
        [
            'email',
            'required',
            'clientScript' => ['class' => MyCustomRequiredClientScript::class],
        ],
    ];
}
```

## Quality code

[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%205-4F5D95.svg?style=for-the-badge&logo=php&logoColor=white)](https://github.com/yii2-framework/jquery/actions/workflows/static.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=styleci&logoColor=white)](https://github.styleci.io/repos/1053295485?branch=main)

## Documentation

- [Installation Guide](docs/guide/installation.md)
- [Configuration Reference](docs/guide/configuration.md)
- [Usage Examples](docs/guide/usage.md)
- [Testing Guide](docs/guide/testing.md)

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?style=for-the-badge&logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=for-the-badge&logo=yii)](https://www.yiiframework.com/)
[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=for-the-badge&logo=telegram)](https://t.me/yii_framework_in_english)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=for-the-badge&logo=slack)](https://yiiframework.com/go/slack)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
