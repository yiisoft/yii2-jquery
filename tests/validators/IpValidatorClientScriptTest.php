<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\validators;

use PHPUnit\Framework\Attributes\Group;
use Yii;
use yii\jquery\tests\data\validators\FakedValidationModel;
use yii\jquery\tests\TestCase;
use yii\validators\IpValidator;
use yii\web\JsExpression;

/**
 * Unit tests for {@see IpValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class IpValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => IpValidator::class]);

        $modelValidator->attrA = '192.168.1.1';

        /** @var string $ipParsePattern */
        $ipParsePattern = $this->invokeMethod($validator, 'getIpParsePattern');

        $ipv4 = $validator->ipv4Pattern;
        $ipv6 = $validator->ipv6Pattern;

        self::assertSame(
            <<<JS
            yii.validation.ip(value, messages, {"ipv4Pattern":$ipv4,"ipv6Pattern":$ipv6,"messages":{"ipv6NotAllowed":"attrA must not be an IPv6 address.","ipv4NotAllowed":"attrA must not be an IPv4 address.","message":"attrA must be a valid IP address.","noSubnet":"attrA must be an IP address with specified subnet.","hasSubnet":"attrA must not be a subnet."},"ipv4":true,"ipv6":true,"ipParsePattern":$ipParsePattern,"negation":false,"subnet":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['ipv4Pattern'] = $clientOptions['ipv4Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv4Pattern']
            : '';
        $clientOptions['ipv6Pattern'] = $clientOptions['ipv6Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv6Pattern']
            : '';
        $clientOptions['ipParsePattern'] = $clientOptions['ipParsePattern'] instanceof JsExpression
            ? (string) $clientOptions['ipParsePattern']
            : '';

        self::assertSame(
            [
                'ipv4Pattern' => $validator->ipv4Pattern,
                'ipv6Pattern' => $validator->ipv6Pattern,
                'messages' => [
                    'ipv6NotAllowed' => 'attrA must not be an IPv6 address.',
                    'ipv4NotAllowed' => 'attrA must not be an IPv4 address.',
                    'message' => 'attrA must be a valid IP address.',
                    'noSubnet' => 'attrA must be an IP address with specified subnet.',
                    'hasSubnet' => 'attrA must not be a subnet.',
                ],
                'ipv4' => true,
                'ipv6' => true,
                'ipParsePattern' => $ipParsePattern,
                'negation' => false,
                'subnet' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-ip', $errorMessage);

        self::assertSame(
            'the input value must be a valid IP address.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithIpv4Only(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => IpValidator::class, 'ipv6' => false]);

        /** @var string $ipParsePattern */
        $ipParsePattern = $this->invokeMethod($validator, 'getIpParsePattern');

        $ipv4 = $validator->ipv4Pattern;
        $ipv6 = $validator->ipv6Pattern;

        self::assertSame(
            <<<JS
            yii.validation.ip(value, messages, {"ipv4Pattern":$ipv4,"ipv6Pattern":$ipv6,"messages":{"ipv6NotAllowed":"attrA must not be an IPv6 address.","ipv4NotAllowed":"attrA must not be an IPv4 address.","message":"attrA must be a valid IP address.","noSubnet":"attrA must be an IP address with specified subnet.","hasSubnet":"attrA must not be a subnet."},"ipv4":true,"ipv6":false,"ipParsePattern":$ipParsePattern,"negation":false,"subnet":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['ipv4Pattern'] = $clientOptions['ipv4Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv4Pattern']
            : '';
        $clientOptions['ipv6Pattern'] = $clientOptions['ipv6Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv6Pattern']
            : '';
        $clientOptions['ipParsePattern'] = $clientOptions['ipParsePattern'] instanceof JsExpression
            ? (string) $clientOptions['ipParsePattern']
            : '';

        self::assertSame(
            [
                'ipv4Pattern' => $validator->ipv4Pattern,
                'ipv6Pattern' => $validator->ipv6Pattern,
                'messages' => [
                    'ipv6NotAllowed' => 'attrA must not be an IPv6 address.',
                    'ipv4NotAllowed' => 'attrA must not be an IPv4 address.',
                    'message' => 'attrA must be a valid IP address.',
                    'noSubnet' => 'attrA must be an IP address with specified subnet.',
                    'hasSubnet' => 'attrA must not be a subnet.',
                ],
                'ipv4' => true,
                'ipv6' => false,
                'ipParsePattern' => $ipParsePattern,
                'negation' => false,
                'subnet' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-ip', $errorMessage);

        self::assertSame(
            'the input value must be a valid IP address.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithIpv6Only(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => IpValidator::class, 'ipv4' => false]);

        /** @var string $ipParsePattern */
        $ipParsePattern = $this->invokeMethod($validator, 'getIpParsePattern');

        $ipv4 = $validator->ipv4Pattern;
        $ipv6 = $validator->ipv6Pattern;

        self::assertSame(
            <<<JS
            yii.validation.ip(value, messages, {"ipv4Pattern":$ipv4,"ipv6Pattern":$ipv6,"messages":{"ipv6NotAllowed":"attrA must not be an IPv6 address.","ipv4NotAllowed":"attrA must not be an IPv4 address.","message":"attrA must be a valid IP address.","noSubnet":"attrA must be an IP address with specified subnet.","hasSubnet":"attrA must not be a subnet."},"ipv4":false,"ipv6":true,"ipParsePattern":$ipParsePattern,"negation":false,"subnet":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['ipv4Pattern'] = $clientOptions['ipv4Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv4Pattern']
            : '';
        $clientOptions['ipv6Pattern'] = $clientOptions['ipv6Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv6Pattern']
            : '';
        $clientOptions['ipParsePattern'] = $clientOptions['ipParsePattern'] instanceof JsExpression
            ? (string) $clientOptions['ipParsePattern']
            : '';

        self::assertSame(
            [
                'ipv4Pattern' => $validator->ipv4Pattern,
                'ipv6Pattern' => $validator->ipv6Pattern,
                'messages' => [
                    'ipv6NotAllowed' => 'attrA must not be an IPv6 address.',
                    'ipv4NotAllowed' => 'attrA must not be an IPv4 address.',
                    'message' => 'attrA must be a valid IP address.',
                    'noSubnet' => 'attrA must be an IP address with specified subnet.',
                    'hasSubnet' => 'attrA must not be a subnet.',
                ],
                'ipv4' => false,
                'ipv6' => true,
                'ipParsePattern' => $ipParsePattern,
                'negation' => false,
                'subnet' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-ip', $errorMessage);

        self::assertSame(
            'the input value must be a valid IP address.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithSubnetRequired(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => IpValidator::class, 'subnet' => true]);

        /** @var string $ipParsePattern */
        $ipParsePattern = $this->invokeMethod($validator, 'getIpParsePattern');

        $ipv4 = $validator->ipv4Pattern;
        $ipv6 = $validator->ipv6Pattern;

        self::assertSame(
            <<<JS
            yii.validation.ip(value, messages, {"ipv4Pattern":$ipv4,"ipv6Pattern":$ipv6,"messages":{"ipv6NotAllowed":"attrA must not be an IPv6 address.","ipv4NotAllowed":"attrA must not be an IPv4 address.","message":"attrA must be a valid IP address.","noSubnet":"attrA must be an IP address with specified subnet.","hasSubnet":"attrA must not be a subnet."},"ipv4":true,"ipv6":true,"ipParsePattern":$ipParsePattern,"negation":false,"subnet":true,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['ipv4Pattern'] = $clientOptions['ipv4Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv4Pattern']
            : '';
        $clientOptions['ipv6Pattern'] = $clientOptions['ipv6Pattern'] instanceof JsExpression
            ? (string) $clientOptions['ipv6Pattern']
            : '';
        $clientOptions['ipParsePattern'] = $clientOptions['ipParsePattern'] instanceof JsExpression
            ? (string) $clientOptions['ipParsePattern']
            : '';

        self::assertSame(
            [
                'ipv4Pattern' => $validator->ipv4Pattern,
                'ipv6Pattern' => $validator->ipv6Pattern,
                'messages' => [
                    'ipv6NotAllowed' => 'attrA must not be an IPv6 address.',
                    'ipv4NotAllowed' => 'attrA must not be an IPv4 address.',
                    'message' => 'attrA must be a valid IP address.',
                    'noSubnet' => 'attrA must be an IP address with specified subnet.',
                    'hasSubnet' => 'attrA must not be a subnet.',
                ],
                'ipv4' => true,
                'ipv6' => true,
                'ipParsePattern' => $ipParsePattern,
                'negation' => false,
                'subnet' => true,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-ip', $errorMessage);

        self::assertSame(
            'the input value must be an IP address with specified subnet.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
