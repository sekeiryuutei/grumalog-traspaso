<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Traspaso $model */

$this->title = 'Actualizar Traspaso: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="traspaso-update">

    <?= $this->render('_form', [
        'model' => $model,
        
    ]) ?>

</div>
