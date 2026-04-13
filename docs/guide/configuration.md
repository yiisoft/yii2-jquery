Configuration
=============

This extension ships a single bootstrap class, `\yii\jquery\Bootstrap`, that wires jQuery-based client-script
implementations into the Yii2 DI container. When the class is registered in the application `bootstrap` array, every
supported validator, grid component, and widget receives a jQuery-backed `$clientScript` automatically.


Registering the bootstrap class
-------------------------------

Add `\yii\jquery\Bootstrap` to the `bootstrap` key of your application configuration:

```php
// config/web.php
return [
    'bootstrap' => [\yii\jquery\Bootstrap::class],
    // ...
];
```

No other configuration is required. The bootstrap runs once at application startup and registers DI container defaults
via `Yii::$container->set()`.


What the bootstrap configures
-----------------------------

`\yii\jquery\Bootstrap` registers the following mappings as DI container defaults. Each listed component is instantiated
with its `clientScript` property pre-set to the matching jQuery implementation:

Validators
----------

| Core validator                          | jQuery client script                                            |
| --------------------------------------- | --------------------------------------------------------------- |
| `yii\validators\BooleanValidator`       | `yii\jquery\validators\BooleanValidatorClientScript`      |
| `yii\validators\CompareValidator`       | `yii\jquery\validators\CompareValidatorClientScript`      |
| `yii\validators\EmailValidator`         | `yii\jquery\validators\EmailValidatorClientScript`        |
| `yii\validators\FileValidator`          | `yii\jquery\validators\FileValidatorClientScript`         |
| `yii\validators\FilterValidator`        | `yii\jquery\validators\FilterValidatorClientScript`       |
| `yii\validators\ImageValidator`         | `yii\jquery\validators\ImageValidatorClientScript`        |
| `yii\validators\IpValidator`            | `yii\jquery\validators\IpValidatorClientScript`           |
| `yii\validators\NumberValidator`        | `yii\jquery\validators\NumberValidatorClientScript`       |
| `yii\validators\RangeValidator`         | `yii\jquery\validators\RangeValidatorClientScript`        |
| `yii\validators\RegularExpressionValidator` | `yii\jquery\validators\RegularExpressionValidatorClientScript` |
| `yii\validators\RequiredValidator`      | `yii\jquery\validators\RequiredValidatorClientScript`     |
| `yii\validators\StringValidator`        | `yii\jquery\validators\StringValidatorClientScript`       |
| `yii\validators\TrimValidator`          | `yii\jquery\validators\TrimValidatorClientScript`         |
| `yii\validators\UrlValidator`           | `yii\jquery\validators\UrlValidatorClientScript`          |

Captcha
-------

| Core component                   | jQuery client script                                         |
| -------------------------------- | ------------------------------------------------------------ |
| `yii\captcha\Captcha`            | `yii\jquery\captcha\CaptchaClientScript`               |
| `yii\captcha\CaptchaValidator`   | `yii\jquery\captcha\CaptchaValidatorClientScript`      |

Grid
----

| Core component                   | jQuery client script                                         |
| -------------------------------- | ------------------------------------------------------------ |
| `yii\grid\CheckboxColumn`        | `yii\jquery\grid\CheckboxColumnClientScript`           |
| `yii\grid\GridView`              | `yii\jquery\grid\GridViewClientScript`                 |

Widgets
-------

| Core component                   | jQuery client script                                         |
| -------------------------------- | ------------------------------------------------------------ |
| `yii\widgets\ActiveForm`         | `yii\jquery\widgets\ActiveFormClientScript`            |


Overriding a single mapping
---------------------------

The bootstrap only registers *default* DI container configurations. You can override the `clientScript` class for a
single instance at any time by passing an explicit `clientScript` configuration. This is the strategy-pattern entry
point; see [usage.md](usage.md) for worked examples.


Next steps
----------

* [Basic usage and examples](usage.md)
* [Testing](testing.md)
