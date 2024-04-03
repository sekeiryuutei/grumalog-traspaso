<?php

$this->registerJs("
$(document).ready(
    function() {
    let tipodocumento = $('#tipodocumento_traspaso').text().trim();
    let consecutivo = $('#consecutivo').text().trim();
    generarCodigoBarras(tipodocumento,'barcodeTipodocumento');
    generarCodigoBarras(consecutivo,'barcodeConsecutivo');

    function generarCodigoBarras(id,barcode) {
        // Eliminar el código de barras anterior
        $('#barcode').empty();
        // Generar el código de barras
        JsBarcode('#'+barcode, id, {
            width: 4, height: 50,
        });
    }

});

");

?>

<div class="d-flex flex-column align-items-baseline" style="margin-top:-10px;">

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
                <?=
                    $model->bodegaOrigen->tipodocumento->tipodocumento->codigo
                    ?>
            </div>
        </h6>

        <h6 class="d-flex flex-row" style="margin-right:5px;">
            NUMERO:
            <div id="consecutivo">
                <?= $model->consecutivo ?>
            </div>
        </h6>

        <h6 style="margin-left:50px">&#160Caja: PKM</h6>

    </div>

    <h6>
        Fecha:
        <?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d-m-Y H:i:s') ?>
    </h6>

    <h6>
        Origen:
        <?= $model->bodegaOrigen->codigo ?>
        <?= $model->bodegaOrigen->nombre; ?>
    </h6>

    <h6>
        Destino:
        <?= $model->bodegaDestino->codigo ?>
        <?= $model->bodegaDestino->nombre; ?>
    </h6>

    <h6>
        Usuario:
        <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
    </h6>

</div>

<hr>

<?PHP
$items = [];
$nroregistro = 1;
$totalGeneral = 0;
$totalPaquetes = 0;
$unidadempaqueNombre = 0;
$unidadempaqueValor = 0;
$categoria = '';
$descipcion = '';

echo '<table border="0">';
echo '<tr><th>REFER.</th><th>COLOR</th><th class="text-center">TALLA</th><th class="text-center">TIPO</th><th class="text-center">CANT</th><th class="text-center">TOTAL/UM</th></tr>';

foreach ($modeldetalles as $detalle) {

    $categoria = explode(' ', $detalle->item->categoria->nombre)[0];
    $descipcion = explode(' ', $detalle->item->descripcion)[0];

    echo '<tr><td>' . $detalle->item->item . '</td>'
        . '<td>' . $detalle->item->color->nombre
        . '</td><td class="text-center">' . $detalle->item->talla->nombre
        . '</td><td class="text-center">' . ($detalle->item->unidadempaque ? $detalle->item->unidadempaque->codigo : $detalle->item->unidadOrden)
        . '</td><td class="text-center">' . $detalle->cantidad
        . '</td><td class="text-center">' . $detalle->cantidad
        * ($detalle->item->unidadempaque ? $detalle->item->unidadempaque->equivalencia : 1)
        . '</td></tr>'
        . ' <tr> <td colspan="12">' . $categoria . ' ' . $descipcion . '</td></tr>';

    $totalPaquetes += $detalle->cantidad;// Acumulamos el valor de la columna "TOTAL" en cada iteración

    $totalGeneral += $detalle->cantidad * ($detalle->item->unidadempaque ? $detalle->item->unidadempaque->equivalencia : 1); // Acumulamos el valor de la columna "TOTAL" en cada iteración

}
echo '
    <tr class="print-border">
        <td colspan="4" style="text-align:left">
            Total unidades:
        </td>
        <td colspan="1" style="text-align:center;"> ' . $totalPaquetes . '</td>' .
    '<td colspan="2" style="text-align:center;">' . $totalGeneral . '</td>
    </tr>';
echo '</table>';
?>

<svg id="barcodeTipodocumento"></svg>

<h1>
    Num.Cajas:
    <?= $model->numeroCajas; ?>
</h1>

<h1>
    Origen:
    <?= $model->bodegaOrigen->codigo ?>
    <?= $model->bodegaOrigen->nombre; ?>
</h1>

<h1>
    Destino:
    <?= $model->bodegaDestino->codigo ?>
    <?= $model->bodegaDestino->nombre; ?>
</h1>

<h1>
    Usuario:
    <?= Yii::$app->user->isGuest ? ' ' : Yii::$app->user->identity->username ?>
</h1>

<svg id="barcodeConsecutivo"></svg>

<div class="d-flex justify-content-start">
    <button class="btn btn-lg btn-primary imprimir-solo" onclick="imprimir()">Confirmar!</button>
</div>

<style>
    .container {
        margin: 0;
        padding-left: 5px !important;
        font-family: "Helvetica";
        /* font-weight: 700; */
        font-size: 16px;
    }

    .print-border {
        border-width: 1px 0px 1px 0px;
        border-color: black;
    }

    th,
    td {
        padding-right: 8px;
        font-family: "Helvetica";
    }

    th {
        font-size: 17px;
    }

    td {
        font-size: 16px;
    }

    table {
        /* border-collapse: separate; */
        font-size: 16px;
        width: 60%;
    }

    td {
        white-space: normal;
        /* Permite saltos de línea */
    }

    h1 {
        font-family: "Helvetica";
        font-size: 29px;
    }

    h6 {
        font-family: "Helvetica";
        font-size: 15px;
    }

    hr {
        margin: 2px;
    }

    #tipodocumento_traspaso {
        margin-left: 2px;
    }

    #w3-collapse {
        justify-content: flex-end;
    }

    @media (max-device-width: 162.6mm) {
        table {
            border-collapse: separate;
            font-size: 10px;
            width: 100%;
        }
        .imprimir-solo {
            display: block !important;
            margin-left: 10px;
        }

        .d-flex.justify-content-start {
            justify-content: center !important;
        }

    }

    @media print {
        .imprimir-solo {
            display: none !important;
        }
    }
</style>

<script>
    function imprimir() {
        // Ocultar el botón de imprimir antes de imprimir
        var botonImprimir = document.querySelector('.imprimir-solo');
        botonImprimir.style.display = 'none';

        // Mandar a imprimir
        window.print();
    }
</script>