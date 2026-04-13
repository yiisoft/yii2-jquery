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
use yii\validators\NumberValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

/**
 * jQuery client-side script for {@see NumberValidator}.
 *
 * @implements ClientValidatorScriptInterface<NumberValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class NumberValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{
     *   pattern: JsExpression,
     *   message: string,
     *   min?: float,
     *   tooSmall?: string,
     *   max?: float,
     *   tooBig?: string,
     *   skipOnEmpty?: int,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $label = $model->getAttributeLabel($attribute);

        $options = [
            'pattern' => new JsExpression(
                $validator->integerOnly ? $validator->integerPattern : $validator->numberPattern,
            ),
            'message' => $validator->getFormattedClientMessage(
                $validator->message,
                ['attribute' => $label],
            ),
        ];

        if ($validator->min !== null) {
            $options['min'] = (float) $validator->min;
            $options['tooSmall'] = $validator->getFormattedClientMessage(
                $validator->tooSmall,
                [
                    'attribute' => $label,
                    'min' => $validator->min,
                ],
            );
        }

        if ($validator->max !== null) {
            $options['max'] = (float) $validator->max;
            $options['tooBig'] = $validator->getFormattedClientMessage(
                $validator->tooBig,
                [
                    'attribute' => $label,
                    'max' => $validator->max,
                ],
            );
        }

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.number(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
