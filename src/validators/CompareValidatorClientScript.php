<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\validators;

use Closure;
use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\CompareValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

/**
 * jQuery client-side script for {@see CompareValidator}.
 *
 * @implements ClientValidatorScriptInterface<CompareValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class CompareValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{
     *   operator: string,
     *   type: string,
     *   message: string,
     *   compareValue?: mixed,
     *   compareAttribute?: string,
     *   compareAttributeName?: string,
     *   skipOnEmpty?: int,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $resolvedCompareValue = $validator->compareValue;

        if ($resolvedCompareValue instanceof Closure) {
            $resolvedCompareValue = $resolvedCompareValue($model, $attribute);
        }

        $options = [
            'operator' => $validator->operator,
            'type' => $validator->type,
        ];

        if ($resolvedCompareValue !== null) {
            $options['compareValue'] = $resolvedCompareValue;
            $compareLabel = $compareValue = $compareValueOrAttribute = $resolvedCompareValue;
        } else {
            $compareAttribute = $validator->compareAttribute === null
                ? "{$attribute}_repeat"
                : $validator->compareAttribute;

            $compareValue = $model->getAttributeLabel($compareAttribute);
            $options['compareAttribute'] = Html::getInputId($model, $compareAttribute);
            $options['compareAttributeName'] = Html::getInputName($model, $compareAttribute);
            $compareLabel = $compareValueOrAttribute = $model->getAttributeLabel($compareAttribute);
        }

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        $options['message'] = $validator->getFormattedClientMessage(
            $validator->message,
            [
                'attribute' => $model->getAttributeLabel($attribute),
                'compareAttribute' => $compareLabel,
                'compareValue' => $compareValue,
                'compareValueOrAttribute' => $compareValueOrAttribute,
            ],
        );

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.compare(value, messages, ' . Json::htmlEncode($options) . ', $form);';
    }
}
