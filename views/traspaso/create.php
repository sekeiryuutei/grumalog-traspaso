<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Traspaso $model */

$this->title = 'Registrar Traspaso';
$this->params['breadcrumbs'][] = ['label' => 'Traspasos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traspaso-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
