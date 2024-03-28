<?php
use app\models\Bodegas;
use app\models\Estadotraspaso;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\search\TraspasoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

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

    @media (max-device-width: 162.6mm) {

        .btn-create {
            font-size: 13px;
            width: 130px !important;
        }

        #w0-filters th:first-of-type {
            display: none;
        }

        /* Ocultar el encabezado de las columnas */
        th[data-col-seq="3"],
        th[data-col-seq="4"],
        th[data-col-seq="7"],
        th[data-col-seq="10"] {
            display: none;
        }
        
        /* Ocultar todas las celdas de las columnas */
        td[data-col-seq="3"],
        td[data-col-seq="4"],
        td[data-col-seq="7"] ,
        td[data-col-seq="10"]{
            display: none;
        }

    }

');

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
        'attribute' => 'codeBodegaOrigen',
        'value' => function ($model) {
            return $model->bodegaOrigen->codigo;
        },
    ],
    [
        'attribute' => 'idBodegaOrigen',
        'value' => function ($model) {
            return $model->bodegaOrigen->nombre;
        },
        'filter' => Bodegas::getListaDataId(['207', '210']),
        'contentOptions' => ['data-cellvalue' => 'idBodegaOrigen'],
    ],
    [
        'attribute' => 'codeBodegaDestino',
        'value' => function ($model) {
            return $model->bodegaDestino->codigo;
        },
    ],
    [
        'attribute' => 'idBodegaDestino',
        'value' => function ($model) {
            return $model->bodegaDestino->nombre;
        },
    ],
    [
        'attribute' => 'numeroCajas',
        'contentOptions' => ['data-cellvalue' => 'numeroCajas',],
    ],
    [
        'attribute' => 'caja',
        'value' => function ($model) {
            return 'PKM';
        },
    ],
    [
        'attribute' => 'idEstado',
        'filter' => Estadotraspaso::getListaData(),
        'value' => function ($model) {
            return $model->estado ? $model->estado->nombre : null;
        },
        'contentOptions' => ['data-cellvalue' => 'idEstado',],
    ],
    [
        'attribute' => 'updated_at',
        'value' => function ($model) {
            $dateTimeParts = explode(' ', $model->created_at);
            return $dateTimeParts[0];
        },
    ],
    [
        'attribute' => 'horaInicio',
        'value' => function ($model) {
            $dateTimeParts = explode(' ', $model->created_at);
            return $dateTimeParts[1];
        },
    ],
    [
        'attribute' => 'fechaUltimoRegistro',
        'value' => function ($model) {
            if ($model->traspasodetalle !== null) {
                $dateTimeParts = explode(' ', $model->traspasodetalle->updated_at);
                return $dateTimeParts[0];
            } else {
                return 'No tiene items asignados';
            }
        },
    ],
    [
        'attribute' => 'horaUltimoRegistro',
        'value' => function ($model) {
            if ($model->traspasodetalle !== null) {
                $dateTimeParts = explode(' ', $model->traspasodetalle->updated_at);
                return $dateTimeParts[1];
            } else {
                return 'No tiene items asignados';
            }
        },
    ],
    [
        'attribute' => 'fechaFin',
        'value' => function ($model) {
            $dateTimeParts = explode(' ', $model->updated_at);
            return $dateTimeParts[0];
        },
    ],
    [
        'attribute' => 'horaFin',
        'value' => function ($model) {
            $dateTimeParts = explode(' ', $model->updated_at);
            return $dateTimeParts[1];
        },
    ],
    [
        'attribute' => 'Und.Empaque',
        'contentOptions' => ['data-cellvalue' => 'Und.Empaque',],
        'value' => function ($model) {
            $totalCantidadPaquetes = 0;
            foreach ($model->traspasodetalles as $detalle) {
                if ($detalle->item->unidadEmpaque != null) {
                    $totalCantidadPaquetes += $detalle->cantidad;
                }
            }
            return $totalCantidadPaquetes;
        },
    ],
    [
        'attribute' => 'Und.Traspaso',
        'contentOptions' => ['data-cellvalue' => 'Und.Traspaso',],
        'value' => function ($model) {
            $totalCantidadPaquetes = 0;
            foreach ($model->traspasodetalles as $detalle) {
                if ($detalle->item->unidadempaque != null) {
                    $totalCantidadPaquetes += $detalle->cantidad * $detalle->item->unidadempaque->equivalencia;
                } else {
                    $totalCantidadPaquetes += $detalle->cantidad;
                }
            }
            return $totalCantidadPaquetes;
        },

    ],

    [
        'attribute' => 'created_by',
        'label' => 'Usuario',
        'value' => function ($model) {
            return $model->usuario ? $model->usuario->username : ' ';
        },
        'contentOptions' => ['data-cellvalue' => 'Usuario',],
    ],
];

?>
<div class="traspaso-index">
    <div class="row">
        <div class="col-lg-6 col-6 derecha">
            <?= Html::a('Crear Traspaso', ['create'], ['class' => 'btn btn-success btn-lg btn-create']) ?>
        </div>
        <div class="col-lg-6 col-6 izquierda">
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
                            'iconOptions' => ['class' => 'text-success btn-create'],
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
        'responsiveWrap' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Mostrando {begin} - {end} de {totalCount} resultados',
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
        'options' => [
            'class' => 'mi-gridview gridview-responsive',
        ],
        'tableOptions' => ['class' => 'table table-bordered table-striped'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',],
            [
                'attribute' => 'serie',
                'contentOptions' => ['data-cellvalue' => 'serie'],
                'value' => function ($model) {
                    return $model->tipodocumento ? $model->tipodocumento->codigo : 'Sin serie';
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
                'filter' => Bodegas::getListaDataId(['207', '210']),
                'contentOptions' => ['data-cellvalue' => 'idBodegaOrigen', 'class' => 'hidden-xs'],
                'value' => function ($model) {
                    return $model->bodegaOrigen->nombre;
                },
            ],
            [
                'attribute' => 'idBodegaDestino',
                'filter' => Bodegas::getListaData(),
                'contentOptions' => ['data-cellvalue' => 'idBodegaDestino', 'class' => 'hidden-xs'],
                'value' => function ($model) {
                    return $model->bodegaDestino->nombre;
                },
            ],
            [
                'attribute' => 'numeroCajas',
                'contentOptions' => ['data-cellvalue' => 'numeroCajas',],
            ],
            [
                'attribute' => 'idEstado',
                'filter' => Estadotraspaso::getListaData(),
                'contentOptions' => ['data-cellvalue' => 'idEstado',],
                'value' => function ($model) {
                    return $model->estado ? $model->estado->nombre : null;
                },
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions' => ['data-cellvalue' => 'updated_at',],
            ],

            [
                'attribute' => 'und_empaque',
                'contentOptions' => ['data-cellvalue' => 'und_empaque',],
                'value' => function ($model) {
                $totalCantidadPaquetes = 0;
                foreach ($model->traspasodetalles as $detalle) {
                    if ($detalle->item->unidadEmpaque != null) {
                        $totalCantidadPaquetes += $detalle->cantidad;
                    }
                }
                return $totalCantidadPaquetes;
            },
            ],
            [
                'attribute' => 'und_traspaso',
                'contentOptions' => ['data-cellvalue' => 'und_traspaso',],
                'value' => function ($model) {
                $totalCantidadPaquetes = 0;
                foreach ($model->traspasodetalles as $detalle) {
                    if ($detalle->item->unidadempaque != null) {
                        $totalCantidadPaquetes += $detalle->cantidad * $detalle->item->unidadempaque->equivalencia;
                    } else {
                        $totalCantidadPaquetes += $detalle->cantidad;
                    }
                }
                return $totalCantidadPaquetes;
            },
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Usuario',
                'contentOptions' => ['data-cellvalue' => 'Usuario'],
                'value' => function ($model) {
                    return $model->usuario ? $model->usuario->username : 'Sin nombre de usuario';
                },
            ],
            [
                'class' => ActionColumn::className(),
                'header' => 'Acción',
                'headerOptions' => ['width' => '15%'],
                'template' => '{detalle} {update} {factura} {anular} ',
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
                            'confirm' => 'Esta seguro de anular este registro? ( Origen: ' 
                            . $model->bodegaOrigen->nombre  . ', Destino: ' 
                            . $model->bodegaDestino->nombre . ', Numero de cajas: '
                            . $model->numeroCajas . ' )',
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
    // Redireccionar después de que se cargue la página si no esta logeado
    window.onload = function () {
        var redirectUrl = '<?= $redirectUrl ?>';
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    };
</script>