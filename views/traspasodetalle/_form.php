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
    // Capturar el código de barras
    $(document).on('input', '#codigo_barras', function() {
        if ($(this).val() !== '') {
            var codigoBarras = $('#codigo_barras').val();
          //  $('#codigo_barras').val(''); // Limpiar el campo
            $('#codigo_barras').focus(); // Colocar el foco en el campo para capturar el siguiente código

            // Simular clic en el botón de registrar
            $('#btn_registrar').trigger('click');

        }
    });
");

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Traspasodetalle $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="traspasodetalle-form">

    <?php $form = ActiveForm::begin(); ?>

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

    <?= $form->field($model, 'idItem')->textInput(['id' => 'codigo_barras']) ?>

    <!--
    <?= $form->field($model, 'cantidad')->textInput(['disabled' => true]) ?>
    -->

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn_registrar']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?= Html::tag('hr', '', ['class' => 'horizontal-line']) ?>

<div class="traspasodetalle-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idTraspaso',
            'idItem',
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