<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\validators;

use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\TrimValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

use function is_array;

/**
 * jQuery client-side script for {@see TrimValidator}.
 *
 * Preserves the `skipOnArray` check from the original validator.
 *
 * @implements ClientValidatorScriptInterface<TrimValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class TrimValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{skipOnArray: bool, skipOnEmpty: bool, chars: false|string}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        return [
            'skipOnArray' => $validator->skipOnArray,
            'skipOnEmpty' => $validator->skipOnEmpty,
            'chars' => $validator->chars === ''
                ? false
                : $validator->chars,
        ];
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        if ($validator->skipOnArray && is_array($model->getAttributes([$attribute])[$attribute] ?? null)) {
            return '';
        }

        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'value = yii.validation.trim($form, attribute, ' . Json::htmlEncode($options) . ', value);';
    }
}
