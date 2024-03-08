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

    .horizontal-line {
        border: none;
        border-top: 1px solid #ccc; /* Color y grosor de la línea */
        margin: 10px 0; /* Espacio alrededor de la línea */
    }
    #tipodocumento_traspaso{
        margin-right: 5px;
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
    });
");
?>
<?= Alert::widget() ?>

<div class="traspasodetalle-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="d-flex flex-row align-items-baseline">
        <?php if ($model->traspaso && $model->traspaso->bodegaOrigen && $model->traspaso->bodegaOrigen->tipodocumento): ?>
            <h1 id="tipodocumento_traspaso">
                <?=
                    $model->traspaso->bodegaOrigen->tipodocumento->tipodocumento->codigo
                    ?>-
            </h1>
        <?php endif; ?>
        </h1>
        <h1 id="consecutivo">
            <?= $model->traspaso->consecutivo ?>
        </h1>
        <h2 style="margin-left:5px;">
            <!-- <?= $model->traspaso->bodegaOrigen->tipodocumento->tipodocumento->consecutivoProximo ?> -->
            <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
        </h2>
    </div>

    <!-- <h1><?= $model->traspaso->idTipoDocumento ?></h1> -->


    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'idTraspaso')->textInput(['disabled' => true]) ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'bodegaorigen')->textInput(['disabled' => true]) ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'bodegadestino')->textInput(['disabled' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'codigoitem')->textInput(['id' => 'codigo_barras', 'autofocus' => true]) ?>
        </div>
    </div>

    <!--
    <?= $form->field($model, 'cantidad')->textInput(['disabled' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn_registrar']) ?>
        <?= Html::Button('Imprimir', ['class' => 'btn btn-info', 'id' => 'btn_Imprimir']) ?>
    </div>
        -->

    <div class="form-group centrar">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn_registrar', 'hidden' => true]) ?>
        <?= Html::a('Imprimir', ['print', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-success btn-lg btn-create', 'target' => '_blank',]) ?>
        <?= Html::a('Terminar', ['end', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-danger btn-lg btn-create mt-1']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<?= Html::tag('hr', '', ['class' => 'horizontal-line']) ?>

<div class="traspasodetalle-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => 'Mostrando {begin} - {end} de {totalCount} resultados',
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
        'options' => [
            'class' => 'mi-gridview', // Agrega una clase CSS a la tabla generada por el GridView
        ],
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'contentOptions' => ['data-cellvalue' => 'id'],

            ],
            [
                'attribute' => 'codigoitem',
                // 'label' => 'Code ean',
                'contentOptions' => ['data-cellvalue' => 'codigoBarras'],
                'value' => function ($model) {
        if ($model->item) {
            return $model->item->codigoBarras;
        }
        return '-';
    }

            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'idItem'],
                'value' => function ($model) {
        if ($model->item) {
            return $model->item->item;
        }
        return '-';
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
        return '-';
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
        return '-';
    }
            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'Unidad Orden'],
                'label' => 'Unidad',
                'value' => function ($model) {
        if ($model->item) {
            return $model->item->unidadOrden;
        }
        return '-';
    }
            ],
            [
                'attribute' => 'cantidad',
                'contentOptions' => ['data-cellvalue' => 'Cantidad'],

            ],
            [
                'attribute' => 'idItem',
                'contentOptions' => ['data-cellvalue' => 'Unidad Empaque'],
                'label' => 'Emapaque',
                'value' => function ($model) {
        if ($model->item->unidadEmpaque) {
            return $model->item->unidadEmpaque;
        }
        return 'UND';
    }
            ],
            [
                'attribute' => 'total',
                'contentOptions' => ['data-cellvalue' => 'Cantidad Total'],
                'value' => function ($model) {
        if ($model->item->unidadEmpaque) {
            return $model->cantidad * $model->item->idunidadempaque->equivalencia;
        }
        return $model->cantidad;
    }
            ],
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Traspasodetalle $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],*/
        ],
    ]); ?>
</div>