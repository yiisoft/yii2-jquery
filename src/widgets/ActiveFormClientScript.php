<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\widgets;

use yii\base\BaseObject;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\client\ClientScriptInterface;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveFormAsset;

use function array_diff_assoc;
use function implode;
use function in_array;
use function is_string;
use function preg_split;

/**
 * jQuery client-side script for {@see ActiveForm} and {@see ActiveField}.
 *
 * Registers the `yii.activeForm` jQuery plugin and encodes form/field validation options.
 *
 * @implements ClientScriptInterface<ActiveForm|ActiveField>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class ActiveFormClientScript extends BaseObject implements ClientScriptInterface
{
    public function getClientOptions(BaseObject $widget, array $params = []): array
    {
        if ($widget instanceof ActiveForm) {
            return $this->getClientOptionsInternal($widget);
        }

        return $this->getClientOptionsForFieldInternal($widget, $params);
    }

    public function getClientOptionsForFieldInternal(ActiveField $field, array $options = []): array
    {
        $attribute = Html::getAttributeName($field->attribute);

        if (!in_array($attribute, $field->model->activeAttributes(), true)) {
            return [];
        }

        $clientValidation = $this->isClientValidationEnabled($field);
        $ajaxValidation = $this->isAjaxValidationEnabled($field);
        $validators = $clientValidation ? $this->buildValidatorJs($field, $attribute) : [];

        if (!$ajaxValidation && (!$clientValidation || $validators === [])) {
            return [];
        }

        $options = $this->resolveSelectors($field, $options);

        if ($ajaxValidation) {
            $options['enableAjaxValidation'] = true;
        }

        foreach (['validateOnChange', 'validateOnBlur', 'validateOnType', 'validationDelay'] as $name) {
            $options[$name] = $field->$name ?? $field->form->$name;
        }

        if ($validators !== []) {
            $options['validate'] = new JsExpression(
                'function (attribute, value, messages, deferred, $form) {' . implode('', $validators) . '}',
            );
        }

        if ($field->addAriaAttributes === false) {
            $options['updateAriaInvalid'] = false;
        }

        return $this->filterDefaults($options);
    }

    public function register(BaseObject $widget, View $view, array $params = []): void
    {
        $id = $widget->options['id'];

        $options = Json::htmlEncode($this->getClientOptions($widget));
        $attributes = Json::htmlEncode($widget->attributes);

        ActiveFormAsset::register($view);

        if (is_string($id) && $id !== '') {
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }
    }

    /**
     * Builds client-side validation JS for each active validator.
     *
     * @return list<string> An array of JavaScript code snippets for client-side validation. Each snippet corresponds to
     * a validator that has client validation enabled and returns non-empty JS code.
     */
    private function buildValidatorJs(ActiveField $field, string $attribute): array
    {
        $validators = [];

        foreach ($field->model->getActiveValidators($attribute) as $validator) {
            $js = $validator->clientValidateAttribute($field->model, $attribute, $field->form->getView());

            if ($validator->enableClientValidation && $js !== '') {
                if ($validator->whenClient !== null) {
                    $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                }

                $validators[] = $js;
            }
        }

        return $validators;
    }

    /**
     * Removes options that match the yii.activeForm.js defaults.
     *
     * @return array<string, mixed> The filtered options with defaults removed.
     */
    private function filterDefaults(array $options): array
    {
        $defaults = [
            'validateOnChange' => true,
            'validateOnBlur' => true,
            'validateOnType' => false,
            'validationDelay' => 500,
            'encodeError' => true,
            'error' => '.help-block',
            'updateAriaInvalid' => true,
        ];

        return array_filter(
            $options,
            static fn (
                mixed $value,
                string $key,
            ): bool => !array_key_exists($key, $defaults) || $defaults[$key] !== $value,
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * Encodes form options that differ from the defaults.
     *
     * @return array<string, mixed> The encoded options for the ActiveForm widget, excluding defaults.
     */
    private function getClientOptionsInternal(ActiveForm $form): array
    {
        $options = [
            'encodeErrorSummary' => $form->encodeErrorSummary,
            'errorSummary' => '.' . implode(
                '.',
                preg_split('/\s+/', $form->errorSummaryCssClass, -1, PREG_SPLIT_NO_EMPTY),
            ),
            'validateOnSubmit' => $form->validateOnSubmit,
            'errorCssClass' => $form->errorCssClass,
            'successCssClass' => $form->successCssClass,
            'validatingCssClass' => $form->validatingCssClass,
            'ajaxParam' => $form->ajaxParam,
            'ajaxDataType' => $form->ajaxDataType,
            'scrollToError' => $form->scrollToError,
            'scrollToErrorOffset' => $form->scrollToErrorOffset,
            'validationStateOn' => $form->validationStateOn,
        ];

        if ($form->validationUrl !== null) {
            $options['validationUrl'] = Url::to($form->validationUrl);
        }

        // only get the options that are different from the default ones (set in yii.activeForm.js)
        return array_diff_assoc(
            $options,
            [
                'encodeErrorSummary' => true,
                'errorSummary' => '.error-summary',
                'validateOnSubmit' => true,
                'errorCssClass' => 'has-error',
                'successCssClass' => 'has-success',
                'validatingCssClass' => 'validating',
                'ajaxParam' => 'ajax',
                'ajaxDataType' => 'json',
                'scrollToError' => true,
                'scrollToErrorOffset' => 0,
                'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_CONTAINER,
            ],
        );
    }

    /**
     * Determines if AJAX validation is enabled for the field.
     *
     * @return bool `true` if AJAX validation is enabled, `false` otherwise.
     */
    private function isAjaxValidationEnabled(ActiveField $field): bool
    {
        if ($field->enableAjaxValidation !== null) {
            return $field->enableAjaxValidation;
        }

        return $field->form->enableAjaxValidation;
    }

    /**
     * Determines if client validation is enabled for the field.
     *
     * @return bool `true` if client validation is enabled, `false` otherwise.
     */
    private function isClientValidationEnabled(ActiveField $field): bool
    {
        if ($field->enableClientValidation !== null) {
            return $field->enableClientValidation;
        }

        return $field->form->enableClientValidation;
    }

    /**
     * Resolves input ID, container, input, and error selectors for the field.
     *
     * @return array<string, mixed> The resolved options with selectors.
     */
    private function resolveSelectors(ActiveField $field, array $options): array
    {
        $inputID = $options['id'] ?? Html::getInputId($field->model, $field->attribute);

        $options['id'] = $inputID;
        $options['name'] = $field->attribute;
        $options['container'] = $field->selectors['container']
            ?? (is_string($inputID) && $inputID !== '' ? ".field-$inputID" : null);
        $options['input'] = $field->selectors['input']
            ?? (is_string($inputID) && $inputID !== '' ? "#$inputID" : null);

        if (isset($field->selectors['error'])) {
            $options['error'] = $field->selectors['error'];
        } elseif (isset($field->errorOptions['class'])) {
            $options['error'] = '.' . implode(
                '.',
                preg_split('/\s+/', $field->errorOptions['class'], -1, PREG_SPLIT_NO_EMPTY),
            );
        } else {
            $options['error'] = $field->errorOptions['tag'] ?? 'span';
        }

        $options['encodeError'] = !isset($field->errorOptions['encode']) || $field->errorOptions['encode'];

        return $options;
    }
}
