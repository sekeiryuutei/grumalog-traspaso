<?php
use app\models\Estadotraspaso;

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
             
    @media (max-width: 650px) {
        tr:first-of-type {
            display:none;
        }
        th, td {
            display:block;
            padding: 5px;
        }
        td::before {
            content: attr(data-cellvalue) ": ";
            font-weight: 700;
            text-transform: capitalize;
        }
        td:first-of-type::before {
            content: "#";
        }
        #w0-filters td:first-of-type::before {
            display: none;
        }
        #w0-filters td:nth-of-type(2)::before {
            content: "id";
        }
        #w0-filters td:nth-of-type(3)::before {
            content: "Bodega origen";
        }
        #w0-filters td:nth-of-type(4)::before {
            content: "Bodega destino";
        }
        #w0-filters td:nth-of-type(5)::before {
            content: "Numero de cajas";
        }
        #w0-filters td:nth-of-type(6)::before {
            content: "Estado";
        }
        #w0-filters td:nth-of-type(7)::before {
            display:none;
        }
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
$this->title = 'Lista de traspasos';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->user->isGuest) {
    // Si el usuario no está autenticado, redirigir al login
    $redirectUrl = Yii::$app->urlManager->createUrl(['site/login']);
}else{
    $redirectUrl = null;
}
?>
<div class="traspaso-index">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <?= Html::a('Crear Traspaso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    // Obtén el modelo del estado eliminado
    $modelEstadoEliminado = Estadotraspaso::findOne(['id' => 3]);

    // Configura el DataProvider para excluir registros con idEstado = 3
    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => Traspaso::find()->where(['!=', 'idEstado', 3])->orderBy(['created_at' => SORT_DESC]),
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Mostrando {begin} - {end} de {totalCount} resultados',
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
        'options' => [
            'class' => 'mi-gridview gridview-responsive',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',],
            [
                'attribute' => 'id',
                'contentOptions' => ['data-cellvalue' => 'id'],
            ],
            [
                'attribute' => 'idBodegaOrigen',
                'value' => function ($model) {
                    return $model->bodegaOrigen->nombre;
                },
                'filter' => Bodegas::getListaData(),
                'contentOptions' => ['data-cellvalue' => 'idBodegaOrigen'],
            ],
            [
                'attribute' => 'idBodegaDestino',
                'value' => function ($model) {
                    return $model->bodegaDestino->nombre;
                },
                'filter' => Bodegas::getListaData(),
                'contentOptions' => ['data-cellvalue' => 'idBodegaDestino'],
            ],
            [
                'attribute' => 'numeroCajas',
                'contentOptions' => ['data-cellvalue' => 'numeroCajas',],
            ],
            [
                'attribute' => 'idEstado',
                'filter' => Estadotraspaso::getListaDataMenosEliminado(),
                'value' => function ($model) {
                    // var_dump($model->estado->nombre);
                    // die ();
                    return $model->estado ? $model->estado->nombre : null;
                },
                'contentOptions' => ['data-cellvalue' => 'idEstado',],
            ],

            [
                'class' => ActionColumn::className(),
                'header' => 'Acción',
                'headerOptions' => ['width' => '15%'],
                'template' => '{update} {detalle} {factura} {delete} {anular} ',
                'contentOptions' => ['data-cellvalue' => 'Acciones',],
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
                    'factura' => function ($url, $model) {
                        return Html::a(
                            '<i class="fa fa-print"></i>',
                            ['factura', 'id' => $model->id],
                            [
                                'title' => 'Ver factura generada',
                                'class' => 'btn btn-default',
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
                                    'confirm' => 'Esta seguro de eliminar este registro? ( Origen:' . $model->bodegaOrigen->nombre . ' Destino: ' .
                                        $model->bodegaDestino->nombre . ' numero de cajas: ' .
                                        $model->numeroCajas . ', al elimarlo se perdera la lista interna de items )',
                                    'method' => 'post',
                                ]
                            ]
                        );
                    },
                    'anular' => function ($url, $model) {
                        return Html::a(
                            '<i class="fa fa-ban"></i>',
                            ['anular', 'id' => $model->id],
                            [
                                'class' => 'btn btn-default',
                                'title' => 'Anular Registro',
                                'data' => [
                                    'confirm' => 'Esta seguro de anular este registro? ( Origen: ' . $model->bodegaOrigen->nombre . ', Destino: ' .
                                        $model->bodegaDestino->nombre . ', Numero de cajas: ' .
                                        $model->numeroCajas . ', al elimarlo se perdera la lista interna de items )',
                                    'method' => 'post',
                                ]
                            ]
                        );
                    },

                ],
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return $model->idEstado == 0; // Condición para mostrar el botón
                    },
                    'detalle' => function ($model, $key, $index) {
                        return $model->idEstado == 0; // Condición para mostrar el botón
                    },
                    'delete' => function ($model, $key, $index) {
                        return $model->idEstado == 0; // Condición para mostrar el botón
                    },
                    'factura' => function ($model, $key, $index) {
                        return $model->idEstado == 1; // Condición para mostrar el botón
                    },
                    'anular' => function ($model, $key, $index) {
                        return $model->idEstado == 1; // Condición para mostrar el botón
                    },
                ],
            ],

        ],
    ]); ?>

</div>

<script>
    // Redireccionar después de que se cargue la página
    window.onload = function () {
        var redirectUrl = '<?= $redirectUrl ?>';
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    };
</script>