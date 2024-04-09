<?php
use diecoding\barcode\generator\Barcode;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<style>
    body {
        margin-left: 10px !important;
    }

    .container {
        margin: 0;
        padding-left: 10px !important;
        font-family: "Helvetica";
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
        margin-left: 10px;
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
</style>

<div class="d-flex flex-column align-items-baseline" style="margin-top:100px;">

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
                <?= $model->bodegaOrigen->tipodocumento->tipodocumento->codigo ?>
            </div>
        </h6>

        <h6 class="d-flex flex-row">
            NUMERO:
            <div id="consecutivo">
                <?= $model->consecutivo ?>
            </div>
        </h6>

        <h6 style="margin-left:20px; margin-right:20px; width: max-content;">
            &#160Caja:
            <?= $isMobile ? 'PKM' : 'PC'; ?>
        </h6>

        <h6 style="display: flex; display:none">Traspaso:
            <div id="traspaso-id">
                <?= $model->id ?>
            </div>
        </h6>


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
        <?= $model->usuario->username ?>
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

<?= Barcode::widget([
    'value' => $model->bodegaOrigen->tipodocumento->tipodocumento->codigo,
]);

?>

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
    <?= $model->usuario->username ?>
</h1>

<?= Barcode::widget([
    'value' => $model->consecutivo,
]);
?>

<div class="col-6">
    <?php $form = ActiveForm::begin(['action' => ['traspasodetalle/impresion',], 'method' => 'post']); ?>

    <?= $form->field($model, 'impresora')->dropDownList(
        $impresoras,
        [
            'prompt' => 'Selecciona una impresora...',
            'id' => 'id-impresora',
            'required' => true
        ]
    ) ?>

    <div class="form-group text-center">
        <?= Html::button('impresion', ['class' => 'btn btn-success btn-lg btn-create', 'id' => 'btn-imprimir']) ?>
    </div>
</div>

<?php
// Agregar script de JavaScript para ejecutar la acción de impresión al hacer clic en el botón "Imprimir"
$this->registerJs("
    // Cuando se haga clic en el botón 'Imprimir'
    $('#btn-imprimir').click(function() {
        // Obtener el valor seleccionado de la impresora
        var impresoraSeleccionada = $('#id-impresora').val();
        var idTraspaso = $('#traspaso-id').text(); // Obtener el ID del traspaso desde el contenido del elemento

        // Verificar si se ha seleccionado una impresora
        if (impresoraSeleccionada) {
            // Ejecutar la acción de impresión
            $.ajax({
                url: '" . Yii::$app->urlManager->createUrl(['/traspasodetalle/impresion']) . "',
                method: 'POST',
                data: {
                    impresoraSeleccionada: impresoraSeleccionada,
                    idTraspaso: idTraspaso,
                },
                success: function(response) {
                    console.log('Impresión ejecutada correctamente');
                },
                error: function(xhr, status, error) {
                    // Manejar errores (opcional)
                    console.error('Error al ejecutar la impresión: ' + error);
                }
            });
        } else {
            // Si no se ha seleccionado una impresora, mostrar un mensaje de error (opcional)
            alert('Por favor, selecciona una impresora antes de imprimir.');
        }
    });
");
?>