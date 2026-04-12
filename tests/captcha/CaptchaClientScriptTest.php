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
use yii\captcha\Captcha;
use yii\captcha\CaptchaAsset;
use yii\jquery\captcha\CaptchaClientScript;
use yii\jquery\tests\data\controllers\SiteController;
use yii\jquery\tests\TestCase;
use yii\web\View;

/**
 * Unit tests for {@see CaptchaClientScript} jQuery client-side script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('captcha')]
final class CaptchaClientScriptTest extends TestCase
{
    public function testGetClientOptions(): void
    {
        ob_start();

        $captcha = Yii::createObject(
            [
                'class' => Captcha::class,
                'name' => 'captcha',
                'captchaAction' => 'site/captcha',
                'imageOptions' => ['id' => 'captcha-image'],
            ],
        );

        ob_end_clean();

        $clientScript = new CaptchaClientScript();

        self::assertSame(
            [
                'refreshUrl' => '/index.php?r=site%2Fcaptcha&refresh=1',
                'hashKey' => 'yiiCaptcha/site/captcha',
            ],
            $clientScript->getClientOptions($captcha),
            'Should return correct client options.',
        );
    }

    public function testRegisterWithClientOptions(): void
    {
        ob_start();

        $captcha = Yii::createObject(
            [
                'class' => Captcha::class,
                'name' => 'captcha',
                'captchaAction' => 'site/captcha',
                'imageOptions' => ['id' => 'captcha-image'],
            ],
        );

        ob_end_clean();

        $view = Yii::$app->view;

        $clientScript = new CaptchaClientScript();

        $clientScript->register($captcha, $view);

        self::assertArrayHasKey(
            CaptchaAsset::class,
            $view->assetBundles,
            'Should register CaptchaAsset.',
        );

        $js = implode('', $view->js[View::POS_READY] ?? []);

        self::assertSame(
            <<<'JS'
            jQuery('#captcha-image').yiiCaptcha({"refreshUrl":"\/index.php?r=site%2Fcaptcha\u0026refresh=1","hashKey":"yiiCaptcha\/site\/captcha"});
            JS,
            $js,
            'Should register jQuery captcha initialization script.',
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Yii::$app->controller = new SiteController('site', Yii::$app);
    }
}
