<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Traspasodetalle $model */

$this->title = 'Adicionar Items';
$this->params['breadcrumbs'][] = ['label' => 'Traspaso', 'url' => ['index', 'idtraspaso' => $model->idTraspaso]];
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
