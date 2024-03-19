<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
use kartik\icons\Icon;

$this->registerCss('
#w3-collapse {
    justify-content: flex-end !important;
  }
.navbar-collapse {
    justify-content: flex-end !important;
}
  ');

Icon::map($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/herpo.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
    <script src="<?= Yii::$app->request->baseUrl ?>/assets/Js/JsBarcode.all.min.js"></script>
</head>
<link href="https://fonts.googleapis.com/css2?family=Curry&display=swap" rel="stylesheet">

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => ['/traspaso/index'],
            'options' => ['class' => 'navbar-dark bg-dark fixed-top navbar-expand-sm']
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => 'Muelle', 'url' => ['/site/muelle']],
                Yii::$app->user->isGuest
                ? ['label' => 'Iniciar Sesion', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                . Html::beginForm(['/site/logout'])
                . Html::submitButton(
                    'Salir (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'nav-link btn btn-link logout d-flex justify-content-end']
                )
                . Html::endForm()
                . '</li>'
            ]
        ]);
        NavBar::end();
        ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container">
            <?php if (!empty ($this->params['breadcrumbs'])): ?>
                <?=
                    Breadcrumbs::widget([
                        'homeLink' => [
                            'label' => 'Hogar',
                            'url' => 'index',
                        ],
                        'links' => $this->params['breadcrumbs'],
                    ]);
                ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy;
                    <?= Yii::$app->params['proyectoNombre'] ?? 'GRUMALOG' ?>
                    <?= date('Y') ?>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!-- <?= Yii::powered() ?> -->
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>