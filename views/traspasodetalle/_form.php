<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\widgets\Alert;

use common\models;

/** @var yii\web\View $this */
/** @var app\models\Traspasodetalle $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss('

    .mi-gridview {
        font-size: 11px; 
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

    .horizontal-line {
        border: none;
        border-top: 1px solid #ccc; /* Color y grosor de la línea */
        margin: 10px 0; /* Espacio alrededor de la línea */
    }

    #tipodocumento_traspaso{
        margin-right: 5px;
    }

');

$this->registerJs("
    $(document).ready(function() {
        // Capturar el código de barras
        $(document).on('input', '#codigo_barras', function() {
            if (e.which == 13 || $(this).val() !== '') {
                var codigoBarras = $('#codigo_barras').val();
            //  $('#codigo_barras').val(''); // Limpiar el campo
                $('#codigo_barras').focus(); // Colocar el foco en el campo para capturar el siguiente código
                // Simular clic en el botón de registrar
                $('#btn_registrar').trigger('click');
            }
        });
");

?>

<?= Alert::widget() ?>

<div class="traspasodetalle-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="d-flex flex-column align-items-baseline">
        <div class="d-flex flex-row align-items-baseline">
            <?php if ($model->traspaso && $model->traspaso->bodegaOrigen && $model->traspaso->bodegaOrigen->tipodocumento): ?>
                
                <h1 id="tipodocumento_traspaso">
                    <?= $model->traspaso->bodegaOrigen->tipodocumento->tipodocumento->codigo ?>-
                </h1>

            <?php endif; ?>

            <h1 id="consecutivo">
                <?= $model->traspaso->consecutivo ?>
            </h1>

        </div>

        <h2 style="margin-left:5px;">
            <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
        </h2>

    </div>

    <div class="row">

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'idTraspaso')->textInput(['disabled' => true]) ?>
        </div>

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'bodegaorigen')->textInput(['disabled' => true]) ?>
        </div>

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'bodegadestino')->textInput(['disabled' => true]) ?>
        </div>

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'count')->textInput(['disabled' => true, 'value' => $count]) ?>
        </div>

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'ultimo_codigo')->textInput(['disabled' => true, 'value' => $ultimo_codigo]) ?>
        </div>

        <div class="col-lg-4 col-sm-6 col-6">
            <?= $form->field($model, 'cantidad_paquetes')->textInput(['disabled' => true, 'value' => $cantidad_paquetes]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'codigoitem')->textInput(['id' => 'codigo_barras', 'autofocus' => true]) ?>
        </div>
    </div>

    <div class="form-group centrar">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn_registrar', 'style' => 'display: none']) ?>
        <?= Html::a('Imprimir', ['print', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-primary btn-lg btn-create', 'target' => '_blank', 'style' => 'display: none']) ?>
        <?= Html::a('Terminar', ['end', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-danger btn-lg btn-create mt-1']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?= Html::tag('hr', '', ['class' => 'horizontal-line']) ?>

<div class="table-responsive">
    <?= GridView::widget([
        'responsiveWrap' => false,//para que no sea responsive
        'dataProvider' => $dataProvider,
        'summary' => 'Mostrando {begin} - {end} de {totalCount} resultados',
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
        'options' => [
            'class' => 'mi-gridview gridview-responsive',
        ],
        'tableOptions' => ['class' => 'table table-bordered table-striped'],
            'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['data-cellvalue' => 'Accion'],
                'header' => 'Eliminar',
                'template' => '{delete}', // Define qué acciones se mostrarán como botones
                'buttons' => [
                    'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="fa fa-trash fa-xs" ></i>',
                                ['delete', 'id' => $model->id, 'idtraspaso' => $model->idTraspaso],
                                [
                                    'class' => 'btn btn-default p-0 d-flex justify-content-center basuraIcon',
                                    'title' => 'Eliminar Registro',
                                    'data' => [
                                        'confirm' => 'Esta seguro de eliminar este registro con codigo de barras: '
                                            . $model->item->codigoBarras . ' talla: ' 
                                            . trim($model->item->talla->nombre)
                                            . ', y cantidad ' . $model->cantidad,
                                        'method' => 'post',
                                    ]
                                ]
                            );
                        }
                ],
            ],
            [
                'attribute' => 'codigoitem',
                'label' => 'codigoitem',
                'contentOptions' => ['data-cellvalue' => 'codigoBarras'],
                'value' => function ($model) {
                        if ($model->item) {
                            return $model->item->codigoBarras;
                        }
                        return 'No existe codigo de barras';
                    }

            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'idItem'],
                'value' => function ($model) {
                        if ($model->item) {
                            return $model->item->item;
                        }
                        return 'No existe item';
                    }
            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'Color'],
                'label' => 'Color',
                'value' => function ($model) {
                        if ($model->item->color) {
                            return $model->item->color->nombre;
                        }
                        return 'No tiene color';
                    }
            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'Talla'],
                'label' => 'Talla',
                'value' => function ($model) {
                        if ($model->item) {
                            return $model->item->talla->nombre;
                        }
                        return 'No tiene talla';
                    }
            ],
            [
                'attribute' => 'unidad',
                'contentOptions' => ['data-cellvalue' => 'Unidad Orden'],
                'value' => function ($model) {
                        if ($model->item->unidadOrden!=null) {
                            return $model->item->unidadOrden;
                        }
                        return $model->item->unidadEmpaque;
                    }
            ],
            [
                'attribute' => 'cantidad',
                'contentOptions' => ['data-cellvalue' => 'Cantidad'],
            ],
            [
                'attribute' => 'totalum',
                'contentOptions' => ['data-cellvalue' => 'Cantidad Total'],
                'value' => function ($model) {
                        if ($model->item->unidadEmpaque) {
                            return $model->cantidad * $model->item->unidadempaque->equivalencia;
                        }
                        return $model->cantidad;
                    }
            ],
        ],
    ]); 
    ?>
    
</div>