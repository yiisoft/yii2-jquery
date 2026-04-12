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
use yii\validators\StringValidator;

/**
 * Unit tests for {@see StringValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class StringValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(
            [
                'class' => StringValidator::class,
                'min' => 3,
                'max' => 10,
            ],
        );

        $modelValidator->attrA = 'test';

        self::assertSame(
            <<<JS
            yii.validation.string(value, messages, {"message":"attrA must be a string.","min":3,"tooShort":"attrA should contain at least 3 characters.","max":10,"tooLong":"attrA should contain at most 10 characters.","skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'message' => 'attrA must be a string.',
                'min' => 3,
                'tooShort' => 'attrA should contain at least 3 characters.',
                'max' => 10,
                'tooLong' => 'attrA should contain at most 10 characters.',
                'skipOnEmpty' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('so', $errorMessage);

        self::assertSame(
            'the input value should contain at least 3 characters.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }

    public function testClientValidateAttributeWithLength(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => StringValidator::class, 'length' => 5]);

        $modelValidator->attrA = 'hello';

        self::assertSame(
            <<<JS
            yii.validation.string(value, messages, {"message":"attrA must be a string.","is":5,"notEqual":"attrA should contain 5 characters.","skipOnEmpty":1});
            JS,
            $validator->clientValidateAttribute($modelValidator, 'attrA', Yii::$app->view),
            'Should return correct validation script.',
        );
        self::assertSame(
            [
                'message' => 'attrA must be a string.',
                'is' => 5,
                'notEqual' => 'attrA should contain 5 characters.',
                'skipOnEmpty' => 1,
            ],
            $validator->getClientOptions($modelValidator, 'attrA'),
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue', $errorMessage);

        self::assertSame(
            'the input value should contain 5 characters.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
