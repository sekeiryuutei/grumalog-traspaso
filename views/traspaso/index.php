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

    .izquierda {
        text-align: left;
    }
 
    .derecha {
        text-align: right;
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
use kartik\export\ExportMenu;
use app\models\Bodegas;

/** @var yii\web\View $this */
/** @var app\models\search\TraspasoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lista de traspasos';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->user->isGuest) {
    // Si el usuario no está autenticado, redirigir al login
    $redirectUrl = Yii::$app->urlManager->createUrl(['site/login']);
} else {
    $redirectUrl = null;
}

$fecha_actual = date("Y-m-d");
$filename = "Relacion_Traspaso_" . $fecha_actual;

$gridColumns = [
    [
        'attribute' => 'consecutivo',
        'value' => function ($model) {
            return $model->consecutivo;
        },
    ],
    [
        'attribute' => 'idBodegaOrigen',
        'value' => function ($model) {
            return $model->bodegaOrigen->nombre;
        },
    ],
    [
        'attribute' => 'idBodegaDestino',
        'value' => function ($model) {
            return $model->bodegaDestino->nombre;
        },
    ],
    'numeroCajas',
    [
        'attribute' => 'idEstado',
        'value' => function ($model) {
            return $model->estado ? $model->estado->nombre : null;
        },
    ],
    [
        'attribute' => 'updated_at',
        'label' => 'Fecha',
        'value' => function ($model) {
            return $model->updated_at;
        },
    ],
    [
        'attribute' => 'created_by',
        'label' => 'Usuario',
        'value' => function ($model) {
            return $model->usuario ? $model->usuario->username : ' ';
        },
    ],
];

?>
<div class="traspaso-index">
    <div class="row">
        <div class="col-lg-6 derecha">
            <?= Html::a('Crear Traspaso', ['create'], ['class' => 'btn btn-success btn-lg btn-create']) ?>
        </div>
        <div class="col-lg-6 izquierda">
            <?php echo ExportMenu::widget(
                [
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'fontAwesome' => true,
                    'filename' => $filename,
                    'dropdownOptions' => [
                        'label' => 'Exportar',
                        'class' => 'btn btn-success btn-lg btn-create',
                    ],
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_EXCEL => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_CSV => false,
                        ExportMenu::FORMAT_EXCEL_X => [
                            'label' => 'Excel 2007+',
                            'icon' => 'file-excel-o',
                            'iconOptions' => ['class' => 'text-success'],
                            'linkOptions' => [],
                            'options' => ['title' => 'Microsoft Excel 2007+ (xlsx)'],
                            'alertMsg' => 'Se va a generar un archivo en formato EXCEL 2007+ (xlsx).',
                            'mime' => 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'extension' => 'xlsx',
                            'writer' => ExportMenu::FORMAT_EXCEL_X
                        ],

                    ]
                ]
            );
            ?>
        </div>
    </div>

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
                'attribute' => 'serie',
                'contentOptions' => ['data-cellvalue' => 'serie'],
                'value' => function ($model) {
                    return $model->tipodocumento ? $model->tipodocumento->codigo : null;
                },
            ],
            [
                'attribute' => 'consecutivo',
                'contentOptions' => ['data-cellvalue' => 'consecutivo'],
                'value' => function ($model) {
                    return $model->consecutivo;
                },
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
                    return $model->estado ? $model->estado->nombre : null;
                },
                'contentOptions' => ['data-cellvalue' => 'idEstado',],
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Fecha',
                'contentOptions' => ['data-cellvalue' => 'updated_at',],
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Usuario',
                // 'filter' => Estadotraspaso::getListaDataMenosEliminado(),
                'value' => function ($model) {
                    return $model->usuario ? $model->usuario->username : ' ';
                },
                'contentOptions' => ['data-cellvalue' => 'Usuario',],
            ],
            [
                'class' => ActionColumn::className(),
                'header' => 'Acción',
                'headerOptions' => ['width' => '15%'],
                'template' => '{update} {detalle} {factura} {anular} ',
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
                    'factura' => function ($model, $key, $index) {
                        return $model->idEstado != null; // Condición para mostrar el botón
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