<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Traspasodetalle $model */

$this->title = 'TRASPASO DE MERCANCIA';
$this->params['breadcrumbs'][] = ['label' => 'Traspaso', 'url' => ['/traspaso/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traspasodetalle-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
