<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\support;

use Yii;
use yii\console\Application;
use yii\helpers\ArrayHelper;
use yii\jquery\Bootstrap;

/**
 * Creates Yii application instances for tests.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
final class ApplicationFactory
{
    private const string COOKIE_VALIDATION_KEY = 'test-cookie-validation-key';

    /**
     * Creates a console application with jQuery Bootstrap configured.
     *
     * @param array<string, mixed> $override
     */
    public static function console(array $override = []): void
    {
        new Application(
            ArrayHelper::merge(
                [
                    'id' => 'testapp',
                    'basePath' => dirname(__DIR__),
                    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
                    'bootstrap' => [Bootstrap::class],
                ],
                $override,
            ),
        );
    }

    /**
     * Destroys the current application.
     */
    public static function destroy(): void
    {
        if (Yii::$app !== null && Yii::$app->has('session', true)) {
            Yii::$app->session->close();
        }

        Yii::$app = null; // @phpstan-ignore assign.propertyType (Yii2 test teardown pattern)
    }

    /**
     * Creates a web application with jQuery Bootstrap configured.
     *
     * @param array<string, mixed> $override
     */
    public static function web(array $override = []): void
    {
        new \yii\web\Application(
            ArrayHelper::merge(self::commonBase(), $override),
        );
    }

    /**
     * @return array<string, mixed> Common configuration for web and console applications.
     */
    private static function commonBase(): array
    {
        return [
            'id' => 'testapp',
            'basePath' => dirname(__DIR__),
            'vendorPath' => dirname(__DIR__, 2) . '/vendor',
            'controllerNamespace' => 'yii\jquery\tests\data\controllers',
            'bootstrap' => [Bootstrap::class],
            'aliases' => [
                '@root' => dirname(__DIR__, 2),
                '@npm' => '@root/node_modules',
                '@tests' => dirname(__DIR__),
            ],
            'components' => [
                'assetManager' => [
                    'basePath' => '@root/runtime/assets',
                    'baseUrl' => '/assets',
                    'hashCallback' => static fn (string $path): string => '5a1b552',
                ],
                'request' => [
                    'cookieValidationKey' => self::COOKIE_VALIDATION_KEY,
                    'scriptFile' => dirname(__DIR__) . '/index.php',
                    'scriptUrl' => '/index.php',
                    'isConsoleRequest' => false,
                ],
            ],
        ];
    }
}
