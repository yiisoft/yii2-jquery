<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\captcha;

use PHPUnit\Framework\Attributes\Group;
use Yii;
use yii\base\DynamicModel;
use yii\captcha\CaptchaValidator;
use yii\helpers\Json;
use yii\jquery\captcha\CaptchaValidatorClientScript;
use yii\jquery\tests\TestCase;
use yii\validators\ValidationAsset;

/**
 * Unit tests for {@see CaptchaValidatorClientScript} jQuery client-side validation script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('captcha')]
final class CaptchaValidatorClientScriptTest extends TestCase
{
    public function testGetClientOptions(): void
    {
        $validator = Yii::createObject(
            [
                'class' => CaptchaValidator::class,
                'captchaAction' => 'site/captcha',
            ],
        );

        $model = new DynamicModel(['captcha']);
        $clientScript = new CaptchaValidatorClientScript();

        $options = $clientScript->getClientOptions($validator, $model, 'captcha');

        self::assertSame(
            'yiiCaptcha/site/captcha',
            $options['hashKey'],
            "Should match expected 'hashKey'.",
        );
        self::assertFalse(
            $options['caseSensitive'],
            "Should return 'caseSensitive' as 'false' by default.",
        );
        self::assertSame(
            'The verification code is incorrect.',
            $options['message'],
            "Should return default error 'message'.",
        );
    }

    public function testRegister(): void
    {
        $validator = Yii::createObject(
            [
                'class' => CaptchaValidator::class,
                'captchaAction' => 'site/captcha',
            ],
        );

        $model = new DynamicModel(['captcha']);
        $view = Yii::$app->view;
        $clientScript = new CaptchaValidatorClientScript();
        $options = Json::htmlEncode($clientScript->getClientOptions($validator, $model, 'captcha'));

        $js = $clientScript->register($validator, $model, 'captcha', $view);

        self::assertArrayHasKey(
            ValidationAsset::class,
            $view->assetBundles,
            'Should register ValidationAsset.',
        );
        self::assertSame(
            <<<JS
            yii.validation.captcha(value, messages, $options);
            JS,
            $js,
            'Should return correct captcha validation script.',
        );
    }
}
