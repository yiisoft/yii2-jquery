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
use yii\validators\UrlValidator;
use yii\web\JsExpression;

/**
 * Unit tests for {@see UrlValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class UrlValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => UrlValidator::class]);

        $modelValidator->attrA = 'https://www.example.com';

        self::assertSame(
            <<<JS
            yii.validation.url(value, messages, {"pattern":/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i,"message":"attrA is not a valid URL.","enableIDN":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';

        self::assertSame(
            [
                'pattern' => '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i',
                'message' => 'attrA is not a valid URL.',
                'enableIDN' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is not a valid URL.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithCustomPattern(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => UrlValidator::class,
                'pattern' => '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i',
            ],
        );

        $modelValidator->attrA = 'example.com';

        self::assertSame(
            <<<JS
            yii.validation.url(value, messages, {"pattern":/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i,"message":"attrA is not a valid URL.","enableIDN":false,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';

        self::assertSame(
            [
                'pattern' => '/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i',
                'message' => 'attrA is not a valid URL.',
                'enableIDN' => false,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is not a valid URL.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithDefaultScheme(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => UrlValidator::class, 'defaultScheme' => 'https']);

        $modelValidator->attrA = 'www.example.com';

        self::assertSame(
            <<<JS
            yii.validation.url(value, messages, {"pattern":/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i,"message":"attrA is not a valid URL.","enableIDN":false,"skipOnEmpty":1,"defaultScheme":"https"});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';

        self::assertSame(
            [
                'pattern' => '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i',
                'message' => 'attrA is not a valid URL.',
                'enableIDN' => false,
                'skipOnEmpty' => 1,
                'defaultScheme' => 'https',
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is not a valid URL.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithEnableIDN(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => UrlValidator::class, 'enableIDN' => true]);

        self::assertSame(
            <<<JS
            yii.validation.url(value, messages, {"pattern":/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i,"message":"attrA is not a valid URL.","enableIDN":true,"skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );

        $clientOptions = $validator->getClientOptions($modelValidator, 'attrA');

        $clientOptions['pattern'] = $clientOptions['pattern'] instanceof JsExpression
            ? (string) $clientOptions['pattern']
            : '';

        self::assertSame(
            [
                'pattern' => '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i',
                'message' => 'attrA is not a valid URL.',
                'enableIDN' => true,
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is not a valid URL.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
