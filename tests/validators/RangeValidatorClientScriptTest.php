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
use yii\validators\RangeValidator;

/**
 * Unit tests for {@see RangeValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class RangeValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => RangeValidator::class,
                'range' => [
                    'apple',
                    'banana',
                    'cherry',
                ],
            ],
        );

        $modelValidator->attrA = 'apple';

        self::assertSame(
            <<<JS
            yii.validation.range(value, messages, {"range":["apple","banana","cherry"],"not":false,"message":"attrA is invalid.","skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'range' => ['apple', 'banana', 'cherry'],
                'not' => false,
                'message' => 'attrA is invalid.',
                'skipOnEmpty' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is invalid.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithAllowArray(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => RangeValidator::class,
                'range' => [
                    'blue',
                    'green',
                    'red',
                ],
                'allowArray' => true,
            ],
        );

        $modelValidator->attrA = ['red', 'blue'];

        self::assertSame(
            <<<JS
            yii.validation.range(value, messages, {"range":["blue","green","red"],"not":false,"message":"attrA is invalid.","skipOnEmpty":1,"allowArray":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'range' => [
                    'blue',
                    'green',
                    'red',
                ],
                'not' => false,
                'message' => 'attrA is invalid.',
                'skipOnEmpty' => 1,
                'allowArray' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate(
            [
                'red',
                'yellow',
            ],
            $errorMessage,
        );

        self::assertSame(
            'the input value is invalid.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithClosureRange(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => RangeValidator::class,
                'range' => static fn (): array => [
                    'dynamic1',
                    'dynamic2',
                    'dynamic3',
                ],
            ],
        );

        $modelValidator->attrA = 'dynamic1';

        self::assertSame(
            <<<JS
            yii.validation.range(value, messages, {"range":["dynamic1","dynamic2","dynamic3"],"not":false,"message":"attrA is invalid.","skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'range' => [
                    'dynamic1',
                    'dynamic2',
                    'dynamic3',
                ],
                'not' => false,
                'message' => 'attrA is invalid.',
                'skipOnEmpty' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value is invalid.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
