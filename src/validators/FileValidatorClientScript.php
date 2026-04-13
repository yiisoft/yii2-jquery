<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\validators;

use Yii;
use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\client\ClientValidatorScriptInterface;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

use function strval;

/**
 * jQuery client-side script for {@see FileValidator}.
 *
 * @template T of \yii\validators\FileValidator
 *
 * @implements ClientValidatorScriptInterface<T>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class FileValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{
     *   message?: string,
     *   skipOnEmpty: bool,
     *   uploadRequired?: string,
     *   mimeTypes?: list<JsExpression>,
     *   wrongMimeType?: string,
     *   extensions?: list<string>,
     *   wrongExtension?: string,
     *   minSize?: int,
     *   tooSmall?: string,
     *   maxSize?: int,
     *   tooBig?: string,
     *   maxFiles?: int,
     *   tooMany?: string,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $label = $model->getAttributeLabel($attribute);

        $options = [];

        if ($validator->message !== null) {
            $options['message'] = $validator->getFormattedClientMessage(
                $validator->message,
                ['attribute' => $label],
            );
        }

        $options['skipOnEmpty'] = $validator->skipOnEmpty;

        if (!$validator->skipOnEmpty) {
            $options['uploadRequired'] = $validator->getFormattedClientMessage(
                $validator->uploadRequired,
                ['attribute' => $label],
            );
        }

        if ($validator->mimeTypes !== null) {
            $mimeTypes = [];

            foreach ($validator->mimeTypes as $mimeType) {
                $mimeTypes[] = new JsExpression(
                    Html::escapeJsRegularExpression($this->buildMimeTypeRegexp($mimeType)),
                );
            }

            $options['mimeTypes'] = $mimeTypes;
            $options['wrongMimeType'] = $validator->getFormattedClientMessage(
                $validator->wrongMimeType,
                [
                    'attribute' => $label,
                    'mimeTypes' => implode(', ', array_map(strval(...), $validator->mimeTypes)),
                ],
            );
        }

        if ($validator->extensions !== null) {
            $options['extensions'] = $validator->extensions;
            $options['wrongExtension'] = $validator->getFormattedClientMessage(
                $validator->wrongExtension,
                [
                    'attribute' => $label,
                    'extensions' => implode(', ', array_map(strval(...), $validator->extensions)),
                ],
            );
        }

        if ($validator->minSize !== null) {
            $options['minSize'] = $validator->minSize;
            $options['tooSmall'] = $validator->getFormattedClientMessage(
                $validator->tooSmall,
                [
                    'attribute' => $label,
                    'limit' => $validator->minSize,
                    'formattedLimit' => Yii::$app->formatter->asShortSize($validator->minSize),
                ],
            );
        }

        if ($validator->maxSize !== null) {
            $options['maxSize'] = $validator->maxSize;
            $options['tooBig'] = $validator->getFormattedClientMessage(
                $validator->tooBig,
                [
                    'attribute' => $label,
                    'limit' => $validator->getSizeLimit(),
                    'formattedLimit' => Yii::$app->formatter->asShortSize($validator->getSizeLimit()),
                ],
            );
        }

        if ($validator->maxFiles !== null) {
            $options['maxFiles'] = $validator->maxFiles;
            $options['tooMany'] = $validator->getFormattedClientMessage(
                $validator->tooMany,
                [
                    'attribute' => $label,
                    'limit' => $validator->maxFiles,
                ],
            );
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.file(attribute, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * Builds the RegExp from the $mask.
     *
     * @return string the regular expression
     */
    protected function buildMimeTypeRegexp(string $mask): string
    {
        return '/^' . str_replace('\*', '.*', preg_quote($mask, '/')) . '$/i';
    }
}
