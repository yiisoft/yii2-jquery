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
use yii\validators\RegularExpressionValidator;
use yii\web\JsExpression;

/**
 * Unit tests for {@see RegularExpressionValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class RegularExpressionValidatorClientScriptTest extends TestCase
{
    public function testClientValidateAttribute(): void
    {
        $modelValidator = new FakedValidationModel();

        $validator = Yii::createObject(['class' => RegularExpressionValidator::class, 'pattern' => '/^[a-zA-Z0-9]+$/']);

        $modelValidator->attrA = 'apple';

        self::assertSame(
            <<<JS
            yii.validation.regularExpression(value, messages, {"pattern":/^[a-zA-Z0-9]+$/,"not":false,"message":"attrA is invalid.","skipOnEmpty":1});
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
                'pattern' => '/^[a-zA-Z0-9]+$/',
                'not' => false,
                'message' => 'attrA is invalid.',
                'skipOnEmpty' => 1,
            ],
            $clientOptions,
            "Should return correct options 'array'.",
        );

        $errorMessage = null;

        $validator->validate('someIncorrectValue!', $errorMessage);

        self::assertSame(
            'the input value is invalid.',
            $errorMessage,
            'Error message should match expected output.',
        );
    }
}
