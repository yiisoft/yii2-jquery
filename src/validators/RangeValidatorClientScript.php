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
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\RangeValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\View;

/**
 * jQuery client-side script for {@see RangeValidator}.
 *
 * @implements ClientValidatorScriptInterface<RangeValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class RangeValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{range: list<mixed>, not: bool, message: string, skipOnEmpty?: int, allowArray?: int}
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        /** @var iterable<int, mixed> $rawRange */
        $rawRange = $validator->range instanceof Closure
            ? ($validator->range)($model, $attribute)
            : $validator->range;

        $range = [];

        foreach ($rawRange as $value) {
            $range[] = $value;
        }

        $options = [
            'range' => $range,
            'not' => $validator->not,
            'message' => $validator->getFormattedClientMessage(
                $validator->message,
                ['attribute' => $model->getAttributeLabel($attribute)],
            ),
        ];

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        if ($validator->allowArray) {
            $options['allowArray'] = 1;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.range(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
