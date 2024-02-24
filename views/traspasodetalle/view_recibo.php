<?php
/** @var yii\web\View $this */
$this->registerJs("

$(document).ready(function() {
    // Funcion para el boton imprimir
    alert('g');
    $(document).on(function() {
        
      //  if ($('#idItem').val() !== '') {
            //capturamos tipodocumento_traspaso desde params (deberia ser desde la bd)
          //  let tipodocumento_traspaso = $('#tipodocumento_traspaso').text();
           // let consecutivo = $('#consecutivo').text();
           // generarCodigoBarras(tipodocumento_traspaso,barcodeTipodocumento);
            generarCodigoBarras('consecutivo',barcodeConsecutivo);
        //}
    });
    
    // Generar el código de barras

    function generarCodigoBarras(id,barcode) {
        alert(id,barcode);
        // Eliminar el código de barras anterior
        $('#barcode').empty();
        // Generar el código de barras
        JsBarcode('#'+barcode, id, {
         //   displayValue: false
        });
    }

});
");

echo $model->bodegaOrigen->nombre;
echo $model->bodegaDestino->nombre;

echo '<p></p>';
echo '<h2>ITEMS</h2>';

$items = [];
$nroregistro = 1;
foreach ($modeldetalles as $detalle) {
    echo $detalle->item->item . ' - ' . $detalle->item->referencia . ' - ' . $detalle->cantidad .'';
}



?>

<h1>vic</h1>
<svg id="barcodeTipodocumento"></svg>

<svg id="barcodeConsecutivo"></svg>