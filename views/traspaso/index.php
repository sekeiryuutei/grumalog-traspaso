<?php

$this->registerCss('
    .mi-gridview {
        font-size: 11px; /* Ajusta el tamaño de la fuente según sea necesario */
        /* Otros estilos CSS según sea necesario */
    }

    .btn-create {
        width: 300px;
    }
    
    .centrar {
        text-align: center;
    }

');

use app\models\Traspaso;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

use app\models\Bodegas;

/** @var yii\web\View $this */
/** @var app\models\search\TraspasoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Traspasos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="traspaso-index">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <?= Html::a('Create Traspaso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Mostrando {begin} - {end} de {totalCount} resultados',
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
        'options' => [
            'class' => 'mi-gridview', // Agrega una clase CSS a la tabla generada por el GridView
        ],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',

            [
                'attribute' => 'idBodegaOrigen', // Nombre del atributo en el modelo
                //'hAlign' => 'center', // Alineación horizontal al centro
                //'vAlign' => 'middle', // Alineación vertical al centro
                'value' => function ($model) {
                        return $model->bodegaOrigen->nombre;
                    },
                'filter' => Bodegas::getListaData()
            ],

            [
                'attribute' => 'idBodegaDestino', // Nombre del atributo en el modelo
                //'hAlign' => 'center', // Alineación horizontal al centro
                //'vAlign' => 'middle', // Alineación vertical al centro
                'value' => function ($model) {
                        return $model->bodegaDestino->nombre;
                    },
                'filter' => Bodegas::getListaData()
            ],

            'numeroCajas',

            [
                'attribute'=> 'idEstado',
                'filter' => ['0' => 'Cerrado', '1' => 'Abierto'],
                'value' => function ($model) {
                    switch ($model->idEstado){
                        case 1: $nombre = 'Abierto'; break;
                        case 0: $nombre = 'Cerrado'; break;
                    };

                    return $nombre;
                }
            ],

            [
                'class' => ActionColumn::className(),
                'header' => 'Acción',
                'headerOptions' => ['width' => '15%'],
                'template' => '{update} {detalle} {delete}',

                'buttons' => [

                    'detalle' => function ($url, $model) {
                            return Html::a(
                                '<i class="fa fa-list"></i>',
                                ['detalle', 'id' => $model->id],
                                [
                                    'title' => 'Registrar Items Traspado',
                                    'class' => 'btn btn-default btn_detalle',
                                ]
                            );
                        },
                    'update' => function ($url, $model) {
                            return Html::a(
                                '<i class="fa fa-edit"></i>',
                                ['update', 'id' => $model->id],
                                [
                                    'title' => 'Actualizar Datos Traspaso',
                                    'class' => 'btn btn-default btn_update',
                                ]
                            );
                        },
                    'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="fa fa-trash"></i>',
                                ['delete', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-default',
                                    'title' => 'Eliminar Registro',
                                    'data' => [
                                        'confirm' => 'Esta Seguro de Eliminar Este Registro? ( OC:' . $model->bodegaOrigen->nombre . '-' .
                                            $model->bodegaDestino->nombre . '-' .
                                            $model->numeroCajas . ' )',
                                        'method' => 'post',
                                    ]
                                ]
                            );
                        },

                ],

                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return $model->idEstado == 1; // Condición para mostrar el botón
                    },
                    'detalle' => function ($model, $key, $index) {
                        return $model->idEstado == 1; // Condición para mostrar el botón
                    },
                ],


            ],

        ],
    ]); ?>


</div>