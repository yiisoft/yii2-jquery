<?php

declare(strict_types=1);

use yii\web\Application;
use yii\web\AssetManager;
use yii\web\Request;
use yii\web\UrlManager;
use yii\web\View;

return [
    'phpstan' => [
        'application_type' => Application::class,
    ],
    'components' => [
        'assetManager' => ['class' => AssetManager::class],
        'request' => ['class' => Request::class],
        'urlManager' => ['class' => UrlManager::class],
        'view' => ['class' => View::class],
    ],
];
