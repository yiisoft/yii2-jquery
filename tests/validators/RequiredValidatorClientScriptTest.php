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
use yii\validators\RequiredValidator;

/**
 * Unit tests for {@see RequiredValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class RequiredValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => RequiredValidator::class]);

        $modelValidator->attrA = 'test_value';

        self::assertSame(
            'yii.validation.required(value, messages, {"message":"attrA cannot be blank."});',
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            ['message' => 'attrA cannot be blank.'],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('', $errorMessage);

        self::assertSame(
            'the input value cannot be blank.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithRequiredValue(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => RequiredValidator::class, 'requiredValue' => 'expected_value']);

        $modelValidator->attrA = 'expected_value';

        self::assertSame(
            <<<JS
            yii.validation.required(value, messages, {"message":"attrA must be \u0022expected_value\u0022.","requiredValue":"expected_value"});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'message' => 'attrA must be "expected_value".',
                'requiredValue' => 'expected_value',
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value must be "expected_value".',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithStrictMode(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => RequiredValidator::class, 'strict' => true]);

        $modelValidator->attrA = 'test_value';

        self::assertSame(
            'yii.validation.required(value, messages, {"message":"attrA cannot be blank.","strict":1});',
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'message' => 'attrA cannot be blank.',
                'strict' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate(null, $errorMessage);

        self::assertSame(
            'the input value cannot be blank.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
