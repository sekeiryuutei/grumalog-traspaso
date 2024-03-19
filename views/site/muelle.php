<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Traspaso $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Lista de traspasos sin enviar';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->user->isGuest) {
    // Si el usuario no está autenticado, redirigir al login
    $redirectUrl = Yii::$app->urlManager->createUrl(['site/login']);
} else {
    $redirectUrl = null;
}


?>
<div class="muelle-form">
<?php $form = ActiveForm::begin(['action' => ['site/muelle']]); ?>

    <?= Alert::widget() ?>

    <div class="row">
        <div class="col-12 col-lg-6">
            <?= $form->field($model, 'serie')->textInput(['id' => 'serie']) ?>
        </div>

        <div class="col-12 col-lg-6">
            <?= $form->field($model, 'consecutivo')->textInput(['id' => 'consecutivo']) ?>
        </div>
    </div>
    <div class="form-group centrar">
        <?= Html::submitButton('Cambiar', ['class' => 'btn btn-success btn-lg btn-create', 'id' => 'btn_registrar']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    // Redireccionar después de que se cargue la página si no esta logueado
    window.onload = function () {
        var redirectUrl = '<?= $redirectUrl ?>';
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    };
</script>