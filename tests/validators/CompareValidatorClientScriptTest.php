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
use yii\validators\CompareValidator;
use yii\validators\ValidationAsset;

/**
 * Unit tests for {@see CompareValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class CompareValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => CompareValidator::class,
                'compareValue' => 'test_value',
                'operator' => '==',
                'type' => CompareValidator::TYPE_STRING,
            ],
        );

        $modelValidator->attrA = 'test_value';

        self::assertSame(
            <<<'JS'
            yii.validation.compare(value, messages, {"operator":"==","type":"string","compareValue":"test_value","skipOnEmpty":1,"message":"attrA must be equal to \u0022test_value\u0022."}, $form);
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'operator' => '==',
                'type' => 'string',
                'compareValue' => 'test_value',
                'skipOnEmpty' => 1,
                'message' => 'attrA must be equal to "test_value".',
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value must be equal to "test_value".',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithClosureCompareValue(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => CompareValidator::class,
                'compareValue' => static fn (): string => 'closure_value',
                'operator' => '==',
                'type' => CompareValidator::TYPE_STRING,
            ],
        );

        self::assertSame(
            <<<'JS'
            yii.validation.compare(value, messages, {"operator":"==","type":"string","compareValue":"closure_value","skipOnEmpty":1,"message":"attrA must be equal to \u0022closure_value\u0022."}, $form);
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'operator' => '==',
                'type' => 'string',
                'compareValue' => 'closure_value',
                'skipOnEmpty' => 1,
                'message' => 'attrA must be equal to "closure_value".',
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value must be equal to "closure_value".',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithCompareAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => CompareValidator::class,
                'compareAttribute' => 'attrA_repeat',
                'operator' => '==',
                'type' => CompareValidator::TYPE_STRING,
            ],
        );

        $modelValidator->attrA = 'test';
        $modelValidator->attrA_repeat = 'test';

        self::assertSame(
            <<<'JS'
            yii.validation.compare(value, messages, {"operator":"==","type":"string","compareAttribute":"fakedvalidationmodel-attra_repeat","compareAttributeName":"FakedValidationModel[attrA_repeat]","skipOnEmpty":1,"message":"attrA must be equal to \u0022attrA_repeat\u0022."}, $form);
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'operator' => '==',
                'type' => 'string',
                'compareAttribute' => 'fakedvalidationmodel-attra_repeat',
                'compareAttributeName' => 'FakedValidationModel[attrA_repeat]',
                'skipOnEmpty' => 1,
                'message' => 'attrA must be equal to "attrA_repeat".',
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );
    }

    public function testClientValidateAttributeWithDefaultCompareAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => CompareValidator::class,
                'operator' => '==',
                'type' => CompareValidator::TYPE_STRING,
            ],
        );

        $modelValidator->attrA = 'test';
        $modelValidator->attrA_repeat = 'test';

        $view = Yii::$app->view;
        $js = $validator->clientValidateAttribute($modelValidator, 'attrA', $view);

        self::assertArrayHasKey(
            ValidationAsset::class,
            $view->assetBundles,
            'Should register ValidationAsset.',
        );
        self::assertStringContainsString(
            'attra_repeat',
            $js,
            'Should use default {attribute}_repeat as compare attribute.',
        );
    }
}
