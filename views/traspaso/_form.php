<?php

use app\models\Bodegas;
use app\models\Traspasouserbodega;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Traspaso $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss('

    .btn-create {
        width: 300px;
    }

    .centrar {
        text-align: center;
    }
    
');

?>

<div class="traspaso-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Alert::widget() ?>

    <div class="row">
        <div class="col-12 col-lg-4">
            <?= $form->field($model, 'idBodegaOrigen')->dropDownList(
                Traspasouserbodega::getListaData(),
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
            <?= $form->field($model, 'numeroCajas')->textInput(
                ['maxlength' => true, 'id' => 'numero-cajas', 'type' => 'number','value' => '1']
            )
            ?>
        </div>

    </div>
    
    <div class="form-group centrar">
        <?= Html::submitButton('Registrar', ['class' => 'btn btn-success btn-lg btn-create']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>