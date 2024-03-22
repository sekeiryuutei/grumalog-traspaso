<?php

/** @var yii\web\View $this */
use yii\helpers\Url; // Importa la clase Url

// Verificar si el usuario está autenticado
if (!Yii::$app->user->isGuest) {
    // Si el usuario está autenticado, redirigir al índice de traspasos
    $redirectUrl = Yii::$app->urlManager->createUrl(['traspaso/index']);
} else {
    // Si el usuario no está autenticado, redirigir al login
    $redirectUrl = Yii::$app->urlManager->createUrl(['site/login']);
}




$this->title = 'grumalog-traspaso';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Modulo traspaso 20!</h1>
        <p><a class="btn btn-lg btn-success" href="/grumalog-traspaso/web/index.php?r=traspaso%2Findex">Traspasos</a>
        </p>
    </div>
</div>

<script>
    // Redireccionar después de que se cargue la página
    window.onload = function () {
        window.location.href = '<?= $redirectUrl ?>';
    };
</script>