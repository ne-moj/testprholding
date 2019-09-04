<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Apples';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Дерево</h1>

        <p class="lead">Поздравляем, Вы на странице вашего дерева, вы можете поменять его размер передав параметры width и height (для задания ширины и высоты кроны дерева)</p>
    </div>

    <div class="body-content">
        <div style='text-align: center'>
            <?= Html::img($image) ?>
        </div>
    </div>
</div>
