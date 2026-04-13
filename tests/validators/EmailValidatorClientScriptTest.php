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
use yii\validators\EmailValidator;
use yii\web\JsExpression;

/**
 * Unit tests for {@see EmailValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class EmailValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(EmailValidator::class);

        $modelValidator->attrA = 'test@example.com';
        $pattern = $validator->pattern;
        $fullPattern = $validator->fullPattern;

        self::assertSame(
            <<<JS
            yii.validation.email(value, messages, {"pattern":$pattern,"fullPattern":$fullPattern,"allowName":false,"message":"attrA is not a valid email address.","enableIDN":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';
        $clientOptions['fullPattern'] = $clientOptions['fullPattern'] instanceof JsExpression
            ? (string) $clientOptions['fullPattern']
            : '';

        self::assertSame(
            [
                'pattern' => $validator->pattern,
                'fullPattern' => $validator->fullPattern,
                'allowName' => false,
                'message' => 'attrA is not a valid email address.',
                'enableIDN' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-email', $errorMessage);

        self::assertSame(
            'the input value is not a valid email address.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithEnableIDN(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => EmailValidator::class, 'enableIDN' => true]);

        $pattern = $validator->pattern;
        $fullPattern = $validator->fullPattern;

        self::assertSame(
            <<<JS
            yii.validation.email(value, messages, {"pattern":$pattern,"fullPattern":$fullPattern,"allowName":false,"message":"attrA is not a valid email address.","enableIDN":true,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';
        $clientOptions['fullPattern'] = $clientOptions['fullPattern'] instanceof JsExpression
            ? (string) $clientOptions['fullPattern']
            : '';

        self::assertSame(
            [
                'pattern' => $validator->pattern,
                'fullPattern' => $validator->fullPattern,
                'allowName' => false,
                'message' => 'attrA is not a valid email address.',
                'enableIDN' => true,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('invalid-email', $errorMessage);

        self::assertSame(
            'the input value is not a valid email address.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
