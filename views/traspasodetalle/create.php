<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Traspasodetalle $model */

$this->title = 'TRASPASO DE MERCANCIA';
$this->params['breadcrumbs'][] = ['label' => 'Traspaso', 'url' => ['/traspaso/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traspasodetalle-create">

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'count' => $count,
        'ultimo_codigo'=> $ultimo_codigo,
        'cantidad_paquetes' => $cantidad_paquetes,
    ]) ?>

</div>
