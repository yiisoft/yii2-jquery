<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\grid;

use yii\base\BaseObject;
use yii\grid\CheckboxColumn;
use yii\helpers\Json;
use yii\web\client\ClientScriptInterface;
use yii\web\View;

use function is_string;

/**
 * jQuery client-side script for {@see CheckboxColumn}.
 *
 * Registers the `yiiGridView('setSelectionColumn', ...)` jQuery plugin call.
 *
 * @implements ClientScriptInterface<CheckboxColumn>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class CheckboxColumnClientScript extends BaseObject implements ClientScriptInterface
{
    public function getClientOptions(BaseObject $widget, array $params = []): array
    {
        return [];
    }

    public function register(BaseObject $widget, View $view, array $params = []): void
    {
        $id = $widget->grid->options['id'];

        $options = Json::encode(
            [
                'name' => $widget->name,
                'class' => $widget->cssClass,
                'multiple' => $widget->multiple,
                'checkAll' => $params['checkAll'] ?? null,
            ],
        );

        if (is_string($id) && $id !== '') {
            $view->registerJs("jQuery('#$id').yiiGridView('setSelectionColumn', $options);");
        }
    }
}
