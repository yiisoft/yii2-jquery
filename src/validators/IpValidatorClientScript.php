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
use yii\validators\IpValidator;
use yii\validators\ValidationAsset;
use yii\validators\Validator;
use yii\web\JsExpression;
use yii\web\View;

/**
 * jQuery client-side script for {@see IpValidator}.
 *
 * @implements ClientValidatorScriptInterface<IpValidator>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class IpValidatorClientScript extends BaseObject implements ClientValidatorScriptInterface
{
    /**
     * @return array{
     *   ipv4Pattern: JsExpression,
     *   ipv6Pattern: JsExpression,
     *   messages: array{
     *     ipv6NotAllowed: string,
     *     ipv4NotAllowed: string,
     *     message: string,
     *     noSubnet: string,
     *     hasSubnet: string,
     *   },
     *   ipv4: bool,
     *   ipv6: bool,
     *   ipParsePattern: JsExpression,
     *   negation: bool,
     *   subnet: bool,
     *   skipOnEmpty?: int,
     * }
     */
    public function getClientOptions(Validator $validator, Model $model, string $attribute): array
    {
        $messages = [
            'ipv6NotAllowed' => $validator->ipv6NotAllowed,
            'ipv4NotAllowed' => $validator->ipv4NotAllowed,
            'message' => $validator->message,
            'noSubnet' => $validator->noSubnet,
            'hasSubnet' => $validator->hasSubnet,
        ];

        foreach ($messages as &$message) {
            $message = $validator->getFormattedClientMessage(
                $message,
                ['attribute' => $model->getAttributeLabel($attribute)],
            );
        }

        $options = [
            'ipv4Pattern' => new JsExpression(
                Html::escapeJsRegularExpression($validator->ipv4Pattern),
            ),
            'ipv6Pattern' => new JsExpression(
                Html::escapeJsRegularExpression($validator->ipv6Pattern),
            ),
            'messages' => $messages,
            'ipv4' => $validator->ipv4,
            'ipv6' => $validator->ipv6,
            'ipParsePattern' => new JsExpression(
                Html::escapeJsRegularExpression($this->getIpParsePattern()),
            ),
            'negation' => $validator->negation,
            'subnet' => $validator->subnet,
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

        return 'yii.validation.ip(value, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * Returns the Regexp pattern for initial IP address parsing.
     */
    private function getIpParsePattern(): string
    {
        return '/^(' . preg_quote(IpValidator::NEGATION_CHAR, '/') . '?)(.+?)(\/(\d+))?$/';
    }
}
