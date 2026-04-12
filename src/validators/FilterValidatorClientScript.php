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
use yii\validators\FilterValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

/**
 * jQuery client-side script for {@see FilterValidator} when the filter is `'trim'`.
 *
 * @implements ClientValidatorScriptInterface<FilterValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class FilterValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{skipOnEmpty?: int}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $options = [];

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        if ($validator->filter !== 'trim' && $validator->filter !== '\trim') {
            return '';
        }

        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'value = yii.validation.trim($form, attribute, ' . Json::htmlEncode($options) . ', value);';
    }
}
