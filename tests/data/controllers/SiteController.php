<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\data\controllers;

use yii\captcha\CaptchaAction;
use yii\web\Controller;

/**
 * Stub controller for testing.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
final class SiteController extends Controller
{
    public function actions(): array
    {
        return [
            'captcha' => ['class' => CaptchaAction::class],
        ];
    }
}
