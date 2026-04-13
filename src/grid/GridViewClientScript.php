<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\grid;

use Closure;
use Yii;
use yii\base\BaseObject;
use yii\grid\GridView;
use yii\grid\GridViewAsset;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\client\ClientScriptInterface;
use yii\web\View;

use function is_string;

/**
 * jQuery client-side script for {@see GridView}.
 *
 * Registers the `yii.gridView` jQuery plugin and encodes filtering options.
 *
 * @implements ClientScriptInterface<GridView>
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
class GridViewClientScript extends BaseObject implements ClientScriptInterface
{
    public function getClientOptions(BaseObject $widget, array $params = []): array
    {
        $filterUrl = $widget->filterUrl ?? Yii::$app->request->url;
        $filterSelector = '';

        $id = $widget->filterRowOptions['id'];

        if (is_string($id) && $id !== '') {
            $filterSelector = "#$id input, #$id select";
        }

        if ($widget->filterSelector !== null) {
            $additionalFilterSelector = $widget->filterSelector;

            if ($widget->filterSelector instanceof Closure) {
                $additionalFilterSelector = ($widget->filterSelector)($widget->getId(), $id);
            }

            $filterSelector = $filterSelector !== ''
                ? "$filterSelector, $additionalFilterSelector"
                : $additionalFilterSelector;

            if ($widget->overrideFilterSelector) {
                $filterSelector = $additionalFilterSelector;
            }
        }

        return [
            'filterUrl' => Url::to($filterUrl),
            'filterSelector' => $filterSelector,
        ];
    }

    public function register(BaseObject $widget, View $view, array $params = []): void
    {
        GridViewAsset::register($view);

        $id = $widget->options['id'];

        $options = Json::htmlEncode(
            [
                ...$this->getClientOptions($widget),
                'filterOnFocusOut' => $widget->filterOnFocusOut,
            ],
        );

        if (is_string($id) && $id !== '') {
            $view->registerJs("jQuery('#$id').yiiGridView($options);");
        }
    }
}
