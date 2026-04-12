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
use yii\validators\FilterValidator;

/**
 * Unit tests for {@see FilterValidatorClientScript} jQuery client-side script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('validators')]
final class FilterValidatorClientScriptTest extends TestCase
{
    public function testGetClientOptionsWithoutSkipOnEmpty(): void
    {
        $validator = Yii::createObject(['class' => FilterValidator::class, 'filter' => 'trim', 'skipOnEmpty' => false]);

        $model = FakedValidationModel::createWithAttributes(['attr_trim' => '  test  ']);

        $clientScript = $validator->clientScript;

        self::assertSame(
            [],
            $clientScript->getClientOptions($validator, $model, 'attr_trim'),
            "Should return empty 'array' when 'skipOnEmpty' is 'false'.",
        );
    }

    public function testGetClientOptionsWithSkipOnEmpty(): void
    {
        $validator = Yii::createObject(['class' => FilterValidator::class, 'filter' => 'trim', 'skipOnEmpty' => true]);

        $model = FakedValidationModel::createWithAttributes(['attr_trim' => '  test  ']);

        $clientScript = $validator->clientScript;

        self::assertSame(
            ['skipOnEmpty' => 1],
            $clientScript->getClientOptions($validator, $model, 'attr_trim'),
            "Should return 'skipOnEmpty' option when enabled.",
        );
    }

    public function testRegisterWithNonTrimFilter(): void
    {
        $validator = Yii::createObject(['class' => FilterValidator::class, 'filter' => 'strtolower']);

        $model = FakedValidationModel::createWithAttributes(['attr_trim' => 'TEST']);

        $clientScript = $validator->clientScript;

        $result = $clientScript->register($validator, $model, 'attr_trim', Yii::$app->view);

        self::assertSame(
            '',
            $result,
            "Should return '' for 'non-trim' filters.",
        );
    }

    public function testRegisterWithTrimFilter(): void
    {
        $validator = Yii::createObject(['class' => FilterValidator::class, 'filter' => 'trim']);

        $model = FakedValidationModel::createWithAttributes(['attr_trim' => '  test  ']);

        $clientScript = $validator->clientScript;

        $js = $clientScript->register($validator, $model, 'attr_trim', Yii::$app->view);

        self::assertSame(
            <<<'JS'
            value = yii.validation.trim($form, attribute, [], value);
            JS,
            $js,
            "Should return correct 'trim' validation script.",
        );
    }

    public function testRegisterWithTrimFilterAndSkipOnEmpty(): void
    {
        $validator = Yii::createObject([
            'class' => FilterValidator::class,
            'filter' => 'trim',
            'skipOnEmpty' => true,
        ]);

        $model = FakedValidationModel::createWithAttributes(['attr_trim' => '  test  ']);

        $js = $validator->clientScript->register($validator, $model, 'attr_trim', Yii::$app->view);

        self::assertSame(
            <<<'JS'
            value = yii.validation.trim($form, attribute, {"skipOnEmpty":1}, value);
            JS,
            $js,
            'Should include skipOnEmpty option in trim validation script.',
        );
    }
}
