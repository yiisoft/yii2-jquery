<?php

declare(strict_types=1);

namespace yii\jquery\validators;

use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\Json;
use yii\validators\BooleanValidator;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

/**
 * jQuery client-side script for {@see BooleanValidator}.
 *
 * @implements ClientValidatorScriptInterface<BooleanValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class BooleanValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{trueValue: mixed, falseValue: mixed, message: string, skipOnEmpty?: int, strict?: int}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $options = [
            'trueValue' => $validator->trueValue,
            'falseValue' => $validator->falseValue,
            'message' => $validator->getFormattedClientMessage(
                $validator->message,
                [
                    'attribute' => $model->getAttributeLabel($attribute),
                    'true' => $validator->trueValue === true ? 'true' : $validator->trueValue,
                    'false' => $validator->falseValue === false ? 'false' : $validator->falseValue,
                ],
            ),
        ];

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        if ($validator->strict) {
            $options['strict'] = 1;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.boolean(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
