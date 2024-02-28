<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

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

<div class="traspasodetalle-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="d-flex flex-row align-items-baseline">
        <h1 id="tipodocumento_traspaso">
            <?php
                // var_dump($model->traspaso->idBodegaOrigen);
            //  $model->bodegaOrigen == 210 ?
            //     Yii::$app->params['tipodocumento_traspaso'] ?? '' :
            //     Yii::$app->params['tipodocumento_crossdocking'] ?? ''
                if($model->traspaso->bodegaOrigen == '210') {
                    echo Yii::$app->params['tipodocumento_traspaso'] ?? '';
                } else {
                    echo Yii::$app->params['tipodocumento_crossdocking'] ?? '';
                }
            ?>
        </h1>
        <h1 id="consecutivo">
            <?= $model->traspaso->consecutivo ?>
        </h1>
        <h2>
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

        <!-- <?= Html::Button('Imprimir JS', ['class' => 'btn btn-info btn-lg btn-create', 'id' => 'btn_Imprimir']) ?> -->
        <?= Html::a('Imprimir', ['print', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-success btn-lg btn-create', 'target' => '_blank',]) ?>
        <?= Html::a('Terminar', ['end', 'idtraspaso' => $model->idTraspaso], ['class' => 'btn btn-success btn-lg btn-create']) ?>
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

            'id',
            'idTraspaso',
            //'idItem',
            [
                'attribute' => 'idItem',
                'value' => function ($model) {
            if ($model->item) {
                return $model->item->item;
            }
            return '-';
        }
            ],

            [
                'attribute' => 'idItem',
                'label' => 'Referencia',
                'value' => function ($model) {
            if ($model->item) {
                return $model->item->referencia;
            }
            return '-';
        }
            ],

            [
                'attribute' => 'idItem',
                'label' => 'Unidad',
                'value' => function ($model) {
            if ($model->item) {
                return $model->item->unidadEmpaque;
            }
            return '-';
        }
            ],

            [
                'attribute' => 'idItem',
                'label' => 'Color',
                'value' => function ($model) {
            if ($model->item->color) {
                return $model->item->color->nombre;
            }
            return '-';
        }
            ],

            'cantidad',
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Traspasodetalle $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],*/
        ],
    ]); ?>
</div>