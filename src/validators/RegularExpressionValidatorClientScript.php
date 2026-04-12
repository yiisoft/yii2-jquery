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
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\RegularExpressionValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

/**
 * jQuery client-side script for {@see RegularExpressionValidator}.
 *
 * @implements ClientValidatorScriptInterface<RegularExpressionValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class RegularExpressionValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{pattern: JsExpression, not: bool, message: string, skipOnEmpty?: int}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $pattern = Html::escapeJsRegularExpression($validator->pattern);

        $options = [
            'pattern' => new JsExpression($pattern),
            'not' => $validator->not,
            'message' => $validator->getFormattedClientMessage(
                $validator->message,
                ['attribute' => $model->getAttributeLabel($attribute)],
            ),
        ];

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.regularExpression(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
