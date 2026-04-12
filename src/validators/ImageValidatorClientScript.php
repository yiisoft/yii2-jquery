<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\validators;

use yii\base\Model;
use yii\helpers\Json;
use yii\validators\ImageValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

/**
 * jQuery client-side script for {@see ImageValidator}.
 *
 * Extends {@see FileValidatorClientScript} to add image-specific dimension validation options.
 *
 * @extends FileValidatorClientScript<ImageValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class ImageValidatorClientScript extends FileValidatorClientScript
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
     *   notImage?: string,
     *   minWidth?: int,
     *   underWidth?: string,
     *   maxWidth?: int,
     *   overWidth?: string,
     *   minHeight?: int,
     *   underHeight?: string,
     *   maxHeight?: int,
     *   overHeight?: string,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $options = parent::getClientOptions($validator, $model, $attribute);

        $label = $model->getAttributeLabel($attribute);

        if ($validator->notImage !== null) {
            $options['notImage'] = $validator->getFormattedClientMessage(
                $validator->notImage,
                ['attribute' => $label],
            );
        }

        if ($validator->minWidth !== null) {
            $options['minWidth'] = $validator->minWidth;
            $options['underWidth'] = $validator->getFormattedClientMessage(
                $validator->underWidth,
                [
                    'attribute' => $label,
                    'limit' => $validator->minWidth,
                ],
            );
        }

        if ($validator->maxWidth !== null) {
            $options['maxWidth'] = $validator->maxWidth;
            $options['overWidth'] = $validator->getFormattedClientMessage(
                $validator->overWidth,
                [
                    'attribute' => $label,
                    'limit' => $validator->maxWidth,
                ],
            );
        }

        if ($validator->minHeight !== null) {
            $options['minHeight'] = $validator->minHeight;
            $options['underHeight'] = $validator->getFormattedClientMessage(
                $validator->underHeight,
                [
                    'attribute' => $label,
                    'limit' => $validator->minHeight,
                ],
            );
        }

        if ($validator->maxHeight !== null) {
            $options['maxHeight'] = $validator->maxHeight;
            $options['overHeight'] = $validator->getFormattedClientMessage(
                $validator->overHeight,
                [
                    'attribute' => $label,
                    'limit' => $validator->maxHeight,
                ],
            );
        }

        return $options;
    }

    public function register(Validator $validator, Model $model, string $attribute, View $view): string
    {
        ValidationAsset::register($view);

        $options = $this->getClientOptions($validator, $model, $attribute);

        return 'yii.validation.image(attribute, messages, ' . Json::htmlEncode($options) . ', deferred);';
    }
}
