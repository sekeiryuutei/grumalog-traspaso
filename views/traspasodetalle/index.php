<?php

use app\models\Traspasodetalle;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\TraspasodetalleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'TRASPASO DE MERCANCIA';
$this->params['breadcrumbs'][] = ['label' => 'Traspaso', 'url' => ['/traspaso/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traspasodetalle-index">

    <p>
        <?= Html::a('Adicionar items al traspaso', ['create', 'idtraspaso' => Yii::$app->request->get('idtraspaso')], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idTraspaso',
            'idItem',
            'cantidad',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Traspasodetalle $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id, 'idtraspaso' => Yii::$app->request->get('idtraspaso')]);
                    }
            ],
        ],
    ]); ?>


</div>