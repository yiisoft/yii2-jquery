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
use yii\validators\PunycodeAsset;
use yii\validators\UrlValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

/**
 * jQuery client-side script for {@see UrlValidator}.
 *
 * @implements ClientValidatorScriptInterface<UrlValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class UrlValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{
     *   pattern: JsExpression,
     *   message: string,
     *   enableIDN: bool,
     *   skipOnEmpty?: int,
     *   defaultScheme?: string,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $pattern = $validator->pattern;

        if (str_contains($validator->pattern, '{schemes}')) {
            $pattern = str_replace(
                '{schemes}',
                '(' . implode(
                    '|',
                    array_map(
                        static fn(mixed $scheme): string => preg_quote((string) $scheme, '/'),
                        $validator->validSchemes,
                    ),
                ) . ')',
                $validator->pattern,
            );
        }

        $options = [
            'pattern' => new JsExpression($pattern),
            'message' => $validator->getFormattedClientMessage(
                $validator->message,
                ['attribute' => $model->getAttributeLabel($attribute)],
            ),
            'enableIDN' => $validator->enableIDN,
        ];

        if ($validator->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        if ($validator->defaultScheme !== null) {
            $options['defaultScheme'] = $validator->defaultScheme;
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        if ($validator->enableIDN) {
            PunycodeAsset::register($view);
        }

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.url(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
