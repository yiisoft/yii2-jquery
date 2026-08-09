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

        self::assertSame(
            [
                'hash' => 1706,
                'hashKey' => 'yiiCaptcha/site/captcha',
                'caseSensitive' => false,
                'message' => 'The verification code is incorrect.',
            ],
            $clientScript->getClientOptions($validator, $model, 'captcha'),
            "Hash must come from the lowercased code; 'skipOnEmpty' must be absent.",
        );
    }

    public function testGetClientOptionsWithCaseSensitive(): void
    {
        $validator = Yii::createObject(
            [
                'class' => CaptchaValidator::class,
                'captchaAction' => 'site/captcha',
                'caseSensitive' => true,
            ],
        );

        $model = new DynamicModel(['captcha']);
        $clientScript = new CaptchaValidatorClientScript();

        self::assertSame(
            [
                'hash' => 1546,
                'hashKey' => 'yiiCaptcha/site/captcha',
                'caseSensitive' => true,
                'message' => 'The verification code is incorrect.',
            ],
            $clientScript->getClientOptions($validator, $model, 'captcha'),
            'Hash must come from the verbatim code.',
        );
    }

    public function testGetClientOptionsWithSkipOnEmpty(): void
    {
        $validator = Yii::createObject(
            [
                'class' => CaptchaValidator::class,
                'captchaAction' => 'site/captcha',
                'skipOnEmpty' => true,
            ],
        );

        $model = new DynamicModel(['captcha']);
        $clientScript = new CaptchaValidatorClientScript();

        self::assertSame(
            [
                'hash' => 1706,
                'hashKey' => 'yiiCaptcha/site/captcha',
                'caseSensitive' => false,
                'message' => 'The verification code is incorrect.',
                'skipOnEmpty' => 1,
            ],
            $clientScript->getClientOptions($validator, $model, 'captcha'),
            'Enabled flag must be emitted as `1`.',
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
