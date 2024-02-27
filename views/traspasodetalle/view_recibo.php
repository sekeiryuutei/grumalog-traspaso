<?php
$this->registerCss('
.container{
    margin:0;
    font-family: "Curry";
    font-weight: 700;
    font-size:9px;
}
table {
    border-collapse: separate;
    border-spacing: 10px 0px;
    font-size:8px;
}
h1{
    font-size:13px;
}
h6{
    font-size:9px;
}
hr{
    margin:2px;
}
');
$this->registerJs("
$(document).ready(function() {
    //capturamos tipodocumento_traspaso desde params (deberia ser desde la bd)
    let tipodocumento_traspaso = $('#tipodocumento_traspaso').text();
    let consecutivo = $('#consecutivo').text();
    generarCodigoBarras(tipodocumento_traspaso,'barcodeTipodocumento');
    generarCodigoBarras(consecutivo,'barcodeConsecutivo');

    // Generar el código de barras
    function generarCodigoBarras(id,barcode) {
        // Eliminar el código de barras anterior
        $('#barcode').empty();
        // Generar el código de barras
        // JsBarcode('#'+barcode, id , {format: 'CODE128B'});
        JsBarcode('#'+barcode, id, {
            width: 0.9, height: 20,
        });
    }
});
");

?>
<div class="d-flex flex-column align-items-baseline">
    <h1>
        <?= Yii::$app->params['tituloTraspaso'] ?? '' ?>
    </h1>
    <h6>
        <?= Yii::$app->params['grupo'] ?? '' ?>
    </h6>
    <div class="d-flex flex-row">
        <h6>NIT: </h6>
        <h6>
            <?= Yii::$app->params['nit'] ?? '' ?>
        </h6>
    </div>
    <div class="d-flex flex-row">
        <h6>
            <?= Yii::$app->params['direccion'] ?? '' ?>
        </h6>
        <h6>&#160TEL:</h6>
        <h6>
            <?= Yii::$app->params['tel'] ?? '' ?>
        </h6>
    </div>
</div>
<hr>
<div class="d-flex flex-column align-items-baseline">
    <div class="d-flex flex-row">
        <h6 class="d-flex flex-row" style="margin-right:50px;">
            Serie:
            <div id="tipodocumento_traspaso">
                <?= Yii::$app->params['tipodocumento_traspaso'] ?? '' ?>
            </div>
        </h6>
        <h6>
            NUMERO:
        </h6>
        <h6 class="pr-3 ml-5" id="consecutivo">
            <?= $model->consecutivo ?>
        </h6>
        <h6 style="margin-left:50px">&#160Caja: PKM</h6>
    </div>
    <h6>
        Origen:
        <?= $model->bodegaOrigen->nombre; ?>
    </h6>
    <h6>
        Destino:
        <?= $model->bodegaDestino->nombre; ?>
    </h6>
    <h6>
        Usuario:
        <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
    </h6>
</div>
<hr>
<?PHP
echo '<p></p>';
echo '<h1>ITEMS</h1>';

$items = [];
$nroregistro = 1;
$totalGeneral = 0;
echo '<table border="0">';
echo '<tr><th>REFER.</th><th>COLOR</th><th>TALLA</th><th>PAQ(FALTA)</th><th>UM</th><th>CANTIDAD</th><th>TOTAL</th></tr>';
foreach ($modeldetalles as $detalle) {
    echo '<tr><td>' . $detalle->item->referencia . '</td><td>' . $detalle->item->color->nombre . '</td><td>'
        . $detalle->item->talla->nombre . '</td><td>' . $detalle->item->unidadEmpaque . '</td><td>'
        . $detalle->item->unidadOrden . '</td><td>' . $detalle->cantidad . '</td><td>' . $detalle->cantidad * $detalle->item->unidadEmpaque . '</td></tr>';
    $totalGeneral += $detalle->cantidad * $detalle->item->unidadEmpaque; // Acumulamos el valor de la columna "TOTAL" en cada iteración

}
echo '<tr><td colspan="6" style="text-align:right">Total General:</td><td>' . $totalGeneral . '</td></tr>';

echo '</table>';
?>

<svg id="barcodeTipodocumento"></svg>

<h1>Num.Cajas:
    <?= $model->numeroCajas; ?>
</h1>
<h1>Origen:
    <?= $model->bodegaOrigen->nombre; ?>
</h1>
<h1>Destino:
    <?= $model->bodegaDestino->nombre; ?>
</h1>
<h6>Usuario:
    <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
</h6>
<svg id="barcodeConsecutivo"></svg>