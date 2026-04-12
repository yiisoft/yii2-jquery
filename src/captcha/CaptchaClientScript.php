<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\captcha;

use yii\base\BaseObject;
use yii\captcha\Captcha;
use yii\captcha\CaptchaAsset;
use yii\helpers\Json;
use yii\web\client\ClientScriptInterface;
use yii\web\View;

use function is_string;

/**
 * jQuery client script for the {@see Captcha}.
 *
 * Registers the CAPTCHA asset bundle and emits the `yiiCaptcha` jQuery plugin initialization JavaScript.
 *
 * @implements ClientScriptInterface<Captcha>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class CaptchaClientScript extends BaseObject implements ClientScriptInterface
{
    public function getClientOptions(BaseObject $widget, array $params = []): array
    {
        return $widget->getClientOptions();
    }

    public function register(BaseObject $widget, View $view, array $params = []): void
    {
        $options = $widget->getClientOptions();

        $options = $options === [] ? '' : Json::htmlEncode($options);

        $id = $widget->imageOptions['id'];

        CaptchaAsset::register($view);

        if (is_string($id) && $id !== '') {
            $view->registerJs("jQuery('#$id').yiiCaptcha($options);");
        }
    }
}
