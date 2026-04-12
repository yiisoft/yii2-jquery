<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\jquery\tests\grid;

use PHPUnit\Framework\Attributes\Group;
use Yii;
use yii\data\ArrayDataProvider;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\jquery\grid\CheckboxColumnClientScript;
use yii\jquery\tests\TestCase;

/**
 * Unit tests for {@see CheckboxColumnClientScript} jQuery client-side script.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
#[Group('jquery')]
#[Group('grid')]
final class CheckboxColumnClientScriptTest extends TestCase
{
    public function testCheckAllWithCustomName(): void
    {
        $config = [
            'id' => 'test-grid',
            'dataProvider' => new ArrayDataProvider(['allModels' => []]),
            'options' => ['id' => 'test-grid'],
            'showHeader' => true,
            'columns' => [
                [
                    'class' => CheckboxColumn::class,
                    'name' => 'custom_selection[]',
                    'multiple' => true,
                ],
            ],
        ];

        $this->assertEqualsWithoutLE(
            <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                </head>
            <body>

            <div id="test-grid">
            <table><thead>
            <tr><th><input type="checkbox" class="select-on-check-all" name="custom_selection_all" value="1"></th></tr>
            </thead>
            <tbody>
            <tr><td colspan="1"><div class="empty">No results found.</div></td></tr>
            </tbody></table>
            </div>
            <script src="/assets/5a1b552/jquery.js"></script>
            <script src="/assets/5a1b552/yii.js"></script>
            <script src="/assets/5a1b552/yii.gridView.js"></script>
            <script>document.addEventListener('DOMContentLoaded', function (event) {
            jQuery('#test-grid').yiiGridView('setSelectionColumn', {"name":"custom_selection[]","class":null,"multiple":true,"checkAll":"custom_selection_all"});
            jQuery('#test-grid').yiiGridView({"filterUrl":"\/","filterSelector":"#test-grid-filters input, #test-grid-filters select","filterOnFocusOut":true});
            });</script></body>
            </html>

            HTML,
            Yii::$app->view->render('@tests/data/views/layout.php', ['content' => GridView::widget($config)]),
            'Rendered HTML does not match expected output',
        );
    }

    public function testCheckAllWithShowHeaderFalse(): void
    {
        $config = [
            'id' => 'test-grid',
            'dataProvider' => new ArrayDataProvider(['allModels' => []]),
            'options' => ['id' => 'test-grid'],
            'showHeader' => false,
            'columns' => [
                [
                    'class' => CheckboxColumn::class,
                    'name' => 'selection',
                    'multiple' => true,
                ],
            ],
        ];

        $this->assertEqualsWithoutLE(
            <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                </head>
            <body>

            <div id="test-grid">
            <table><tbody>
            <tr><td colspan="1"><div class="empty">No results found.</div></td></tr>
            </tbody></table>
            </div>
            <script src="/assets/5a1b552/jquery.js"></script>
            <script src="/assets/5a1b552/yii.js"></script>
            <script src="/assets/5a1b552/yii.gridView.js"></script>
            <script>document.addEventListener('DOMContentLoaded', function (event) {
            jQuery('#test-grid').yiiGridView('setSelectionColumn', {"name":"selection[]","class":null,"multiple":true,"checkAll":null});
            jQuery('#test-grid').yiiGridView({"filterUrl":"\/","filterSelector":"#test-grid-filters input, #test-grid-filters select","filterOnFocusOut":true});
            });</script></body>
            </html>

            HTML,
            Yii::$app->view->render('@tests/data/views/layout.php', ['content' => GridView::widget($config)]),
            'Rendered HTML does not match expected output',
        );
    }

    public function testRegister(): void
    {
        $config = [
            'id' => 'test-grid',
            'dataProvider' => new ArrayDataProvider(['allModels' => []]),
            'options' => ['id' => 'test-grid'],
            'columns' => [
                [
                    'class' => CheckboxColumn::class,
                    'name' => 'selection',
                    'multiple' => true,
                    'cssClass' => 'checkbox-class',
                ],
            ],
        ];

        $this->assertEqualsWithoutLE(
            <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                </head>
            <body>

            <div id="test-grid">
            <table><thead>
            <tr><th><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th></tr>
            </thead>
            <tbody>
            <tr><td colspan="1"><div class="empty">No results found.</div></td></tr>
            </tbody></table>
            </div>
            <script src="/assets/5a1b552/jquery.js"></script>
            <script src="/assets/5a1b552/yii.js"></script>
            <script src="/assets/5a1b552/yii.gridView.js"></script>
            <script>document.addEventListener('DOMContentLoaded', function (event) {
            jQuery('#test-grid').yiiGridView('setSelectionColumn', {"name":"selection[]","class":"checkbox-class","multiple":true,"checkAll":"selection_all"});
            jQuery('#test-grid').yiiGridView({"filterUrl":"\/","filterSelector":"#test-grid-filters input, #test-grid-filters select","filterOnFocusOut":true});
            });</script></body>
            </html>

            HTML,
            Yii::$app->view->render('@tests/data/views/layout.php', ['content' => GridView::widget($config)]),
            'Rendered HTML does not match expected output',
        );
    }

    public function testRegisterWithClientScriptOptions(): void
    {
        $gridView = Yii::createObject(
            [
                'class' => GridView::class,
                'dataProvider' => new ArrayDataProvider(['allModels' => []]),
                'options' => ['id' => 'test-grid'],
            ],
        );

        $checkboxColumn = Yii::createObject(
            [
                'class' => CheckboxColumn::class,
                'cssClass' => 'custom-class',
                'grid' => $gridView,
                'multiple' => false,
                'name' => 'customSelection',
            ],
        );

        $checkboxColumn->clientScript->register($checkboxColumn, Yii::$app->view);

        self::assertInstanceOf(
            CheckboxColumnClientScript::class,
            $checkboxColumn->clientScript,
            "Should have 'CheckboxColumnClientScript' instance.",
        );
        self::assertEmpty(
            $checkboxColumn->clientScript->getClientOptions($checkboxColumn),
            "Should always return empty 'array'.",
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['REQUEST_URI'] = 'https://example.com/';
    }
}
