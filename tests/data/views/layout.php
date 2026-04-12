<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

/**
 * @var string $content
 * @var \yii\web\View $this
 */
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

<?= $content ?>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();
