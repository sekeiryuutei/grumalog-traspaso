<?php

$this->registerCss('

    .btn-create {
        width: 300px;
    }

    .centrar {
        text-align: center;
    }
    
');

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

use app\models\Bodegas;

/** @var yii\web\View $this */
/** @var app\models\Traspaso $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="traspaso-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Alert::widget() ?>

    <div class="row">
        <div class="col-12 col-lg-4">
            <?= $form->field($model, 'idBodegaOrigen')->dropDownList(
                Bodegas::getListaDataId(['207','210']),
                [
                    'prompt' => ' Bodega Origen ... ',
                    'id' => 'id-bodega-origen',
                    'required' => true
                ]
            )
                ?>
        </div>

        <div class="col-12 col-lg-4">
            <?= $form->field($model, 'idBodegaDestino')->dropDownList(
                Bodegas::getListaData(),
                [
                    'prompt' => ' Bodega Destino ... ',
                    'id' => 'id-bodega-destino',
                    'required' => true
                ]
            )
                ?>
        </div>

        <div class="col-12 col-lg-4">
            <?= $form->field($model, 'numeroCajas')->textInput(['maxlength' => true, 'id' => 'numero-cajas','type' => 'number',]) ?>
        </div>
    </div>
    <div class="form-group centrar">
        <?= Html::submitButton('Registrar', ['class' => 'btn btn-success btn-lg btn-create']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>