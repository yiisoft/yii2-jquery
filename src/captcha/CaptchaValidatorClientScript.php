<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\captcha;

use yii\base\BaseObject;
use yii\base\Model;
use yii\captcha\CaptchaValidator;
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

/**
 * jQuery client-side script for {@see CaptchaValidator}.
 *
 * @implements ClientValidatorScriptInterface<CaptchaValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class CaptchaValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{hash: int, hashKey: string, caseSensitive: bool, message: string, skipOnEmpty?: int}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        return $validator->getClientOptions($model, $attribute);
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.captcha(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
