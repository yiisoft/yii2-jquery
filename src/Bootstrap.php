<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Bootstraps the jQuery integration layer.
 *
 * Configures the DI container with jQuery-based `$clientScript` defaults for all validators and widgets that support
 * the strategy pattern.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
final class Bootstrap implements BootstrapInterface
{
    /**
     * Maps core validators, widgets, and grid components to their jQuery client-script implementations.
     *
     * Each key is a fully qualified class name from `yiisoft/yii2`, and each value is the corresponding jQuery
     * client-script class from this package. During {@see bootstrap()}, these are registered as DI container defaults
     * so that `$clientScript` is automatically configured when the component is instantiated.
     */
    private const array CLIENT_SCRIPT_MAP = [
        // Captcha.
        \yii\captcha\Captcha::class => captcha\CaptchaClientScript::class,
        \yii\captcha\CaptchaValidator::class => captcha\CaptchaValidatorClientScript::class,
        // Grid.
        \yii\grid\CheckboxColumn::class => grid\CheckboxColumnClientScript::class,
        \yii\grid\GridView::class => grid\GridViewClientScript::class,
        // Validators.
        \yii\validators\BooleanValidator::class => validators\BooleanValidatorClientScript::class,
        \yii\validators\CompareValidator::class => validators\CompareValidatorClientScript::class,
        \yii\validators\EmailValidator::class => validators\EmailValidatorClientScript::class,
        \yii\validators\FileValidator::class => validators\FileValidatorClientScript::class,
        \yii\validators\FilterValidator::class => validators\FilterValidatorClientScript::class,
        \yii\validators\ImageValidator::class => validators\ImageValidatorClientScript::class,
        \yii\validators\IpValidator::class => validators\IpValidatorClientScript::class,
        \yii\validators\NumberValidator::class => validators\NumberValidatorClientScript::class,
        \yii\validators\RangeValidator::class => validators\RangeValidatorClientScript::class,
        \yii\validators\RegularExpressionValidator::class => validators\RegularExpressionValidatorClientScript::class,
        \yii\validators\RequiredValidator::class => validators\RequiredValidatorClientScript::class,
        \yii\validators\StringValidator::class => validators\StringValidatorClientScript::class,
        \yii\validators\TrimValidator::class => validators\TrimValidatorClientScript::class,
        \yii\validators\UrlValidator::class => validators\UrlValidatorClientScript::class,
        // Widgets.
        \yii\widgets\ActiveForm::class => widgets\ActiveFormClientScript::class,
    ];

    public function bootstrap($app): void
    {
        foreach (self::CLIENT_SCRIPT_MAP as $component => $clientScript) {
            Yii::$container->set($component, ['clientScript' => ['class' => $clientScript]]);
        }
    }
}
