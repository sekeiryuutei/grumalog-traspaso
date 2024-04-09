<?php

namespace app\controllers;

use app\models\Impresora;
use app\models\Item;
use app\models\search\TraspasodetalleSearch;
use app\models\Traspaso;
use app\models\Traspasodetalle;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use xstreamka\mobiledetect\Device;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/** @var yii\widgets\ActiveForm $form */

/**
 * TraspasodetalleController implements the CRUD actions for Traspasodetalle model.
 */
class TraspasodetalleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
    /**
     * Lists all Traspasodetalle models.
     *
     * @return string
     */
    public function actionIndex($idtraspaso = 0)
    {
        $searchModel = new TraspasodetalleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $idtraspaso);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'idtraspaso' => $idtraspaso,
        ]);
    }

    /**
     * Displays a single Traspasodetalle model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Traspasodetalle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($idtraspaso)
    {
        $modeltraspaso = Traspaso::findOne(['id' => $idtraspaso]);

        if ($modeltraspaso->idEstado != 0) {
            return $this->redirect(['/traspaso/index']);
        }

        $ultimo_codigo = null;

        if ($modeltraspaso->idUltimoItem != null) {
            $modelitem = Item::find()
                ->where(['id' => $modeltraspaso->idUltimoItem])
                ->andWhere(['idEstado' => 'ACTIVO'])
                ->one();
            $ultimo_codigo = $modelitem->codigoBarras;
        }

        $cantidad_paquetes = Traspasodetalle::find()
            ->alias('td')
            ->join('INNER JOIN', 'item as it', 'td.idItem = it.id')
            ->where(['idTraspaso' => $idtraspaso])
            ->andWhere('unidadEmpaque IS NOT NULL')
            ->sum('cantidad');


        $model = new Traspasodetalle();
        $model->idTraspaso = $idtraspaso;
        $model->cantidad = 1;

        $count = Traspasodetalle::find()
            ->alias('td')
            ->select([
                'total' => new \yii\db\Expression('SUM(
                CASE
                    WHEN ue.equivalencia IS NOT NULL THEN td.cantidad * ue.equivalencia
                    ELSE td.cantidad
                END
            )')
            ])
            ->innerJoin('item as it', 'td.idItem = it.id')
            ->leftJoin('unidadEmpaque as ue', 'ue.codigo = it.unidadEmpaque')
            ->where(['idTraspaso' => $idtraspaso])
            ->scalar();


        $model->bodegaorigen = $model->traspaso->bodegaOrigen->nombre;
        $model->bodegadestino = $model->traspaso->bodegaDestino->nombre;

        $searchModel = new TraspasodetalleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $idtraspaso);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $modelitem = Item::find()
                    ->where(['codigoBarras' => $model->codigoitem])
                    ->andWhere(['idEstado' => 'ACTIVO'])
                    ->one();

                if ($modelitem == null) {

                    Yii::$app->session->setFlash('error', 'No existe codigo de barras: ' . $model->codigoitem);

                } else {

                    $model->idItem = $modelitem->id;
                    $modeldetalle = Traspasodetalle::find()->where([
                        'idTraspaso' => $idtraspaso,
                        'idItem' => $model->idItem
                    ])->one();

                    if ($modeldetalle == null) {
                        $modeldetalle = new Traspasodetalle();
                        $modeldetalle->idTraspaso = $model->idTraspaso;
                        $modeldetalle->idItem = $model->idItem;
                        $modeldetalle->cantidad = 0;
                    }

                    $inventario = $modeldetalle->getInventario($model->codigoitem, $model->traspaso->bodegaOrigen->codigo);

                    if ($inventario > 0) {

                        $modeldetalle->codigoitem = $model->idItem;
                        $modeldetalle->cantidad = $modeldetalle->cantidad + $model->cantidad;

                        Yii::debug('Guardando el modelo detalle', __METHOD__);

                        if ($modeldetalle->validate()) {

                            Yii::debug('Modelo válido, guardando', __METHOD__);
                            $modeldetalle->save();

                            $modeltraspaso->idUltimoItem = $modeldetalle->idItem;
                            $modeltraspaso->save();

                            Yii::$app->session->setFlash('success', 'Guardado exitosamente!');
                            Yii::debug('Modelo guardado correctamente', __METHOD__);

                        } else {

                            Yii::debug('El modelo no es válido. Verifica los datos.', __METHOD__);

                            Yii::$app->session->setFlash('error', 'El modelo no es válido, verifica los datos.' . __METHOD__);

                        }
                    } else {

                        Yii::$app->session->setFlash('error', 'Articulo sin existencia para traspaso: ' . $model->codigoitem . ' en bodega ' . $model->bodegaorigen . ' inv ' . $inventario);
                    }

                    return $this->redirect(['create', 'idtraspaso' => $idtraspaso]);

                }
                return $this->redirect(['create', 'idtraspaso' => $idtraspaso]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'count' => $count,
            'ultimo_codigo' => $ultimo_codigo,
            'cantidad_paquetes' => $cantidad_paquetes,
        ]);
    }



    /**
     * Updates an existing Traspasodetalle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new TraspasodetalleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEnd($idtraspaso)
    {
        $model = Traspaso::findOne(['id' => $idtraspaso]);
        $model->idEstado = 1;
        $model->save();

        $modeldetalles = $model->traspasodetalles;

        $model = Traspaso::findOne(['id' => $idtraspaso]);

        return $this->render('view_recibo', [
            'model' => $model,
            'modeldetalles' => $modeldetalles,
        ]);
    }

    public function actionPrint($idtraspaso)
    {
        $model = Traspaso::findOne(['id' => $idtraspaso]);
        $modeldetalles = $model->traspasodetalles;

        if (empty($modeldetalles)) {
            return $this->redirect(['/traspaso/index']);
        };

        $impresoras = Impresora::getListaData();
        $isMobile = Device::$isMobile;

        return $this->render('view_recibo', [
            'model' => $model,
            'modeldetalles' => $modeldetalles,
            'impresoras' => $impresoras,
            'isMobile' => $isMobile
        ]);
    }

    public function actionImpresion()
    {
        $impresoraSeleccionada = Yii::$app->request->post('impresoraSeleccionada');
        $idTraspaso = Yii::$app->request->post('idTraspaso');

        $model = Traspaso::findOne(['id' => (int) $idTraspaso]);
        $modeldetalles = $model->traspasodetalles;

        $isMobile = Device::$isMobile;

        $impresora = Impresora::findOne(['id' => (int) $impresoraSeleccionada]);

        try {
            //conectarse a la impresora
            $connector = new NetworkPrintConnector($impresora->ip, "9100");
            $printer = new Printer($connector);
            //variables para factura
            $totalGeneral = 0;
            $totalPaquetes = 0;
            $serie = $model->bodegaOrigen->tipodocumento->tipodocumento->codigo;
            $numero_serie = $model->consecutivo;
            $categoria = '';
            $descipcion = '';
            //primera parte de la factura
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
            $printer->text(Yii::$app->params['tituloTraspaso'] . "\n \n");
            $printer->selectPrintMode();
            $heights = array(1, 2, 4, 8, 16, 32);
            $widths = array(1, 2, 3, 4, 5, 6, 7, 8);
            $printer->text(Yii::$app->params['grupo'] . "\n");
            $printer->text("NIT: " . Yii::$app->params['nit'] . " \n");
            $printer->text("Direccion: " . Yii::$app->params['direccion'] . ' ' . "TEL: " . Yii::$app->params['tel'] . "\n");
            $printer->text("__________________________________________\n");
            $printer->text("SERIE:" . $serie . "   NUMERO:" . $numero_serie . "   CAJA:" . $isMobile ? 'PKM' : 'PC' . "\n");
            $printer->text("FECHA:" . Yii::$app->formatter->asDatetime($model->updated_at, 'php:d-m-Y H:i:s') . "\n");
            $printer->text("ORIGEN:" . trim($model->bodegaOrigen->codigo) . ' ' . $model->bodegaOrigen->nombre . "\n");
            $printer->text("DESTINO:" . trim($model->bodegaDestino->codigo) . ' ' . $model->bodegaDestino->nombre . "\n");
            $printer->text("USUARIO:" . $model->usuario->username . "\n");
            $printer->text("________________________________________\n");
            // Encabezados lista items
            $encabezados = "REFER.  COLOR  TALLA  TIPO  CANT  TOTAL/UM\n";
            $printer->text($encabezados);

            foreach ($modeldetalles as $detalle) {
                // Obtener los datos del detalle
                $categoria = explode(' ', $detalle->item->categoria->nombre)[0];
                $descipcion = explode(' ', $detalle->item->descripcion)[0];
                $referencia = $detalle->item->item;
                $color = explode(' ', $detalle->item->color->nombre)[0];
                $talla = $detalle->item->talla->nombre;
                $cantidad = $detalle->cantidad;
                $total = $cantidad * ($detalle->item->unidadempaque ? $detalle->item->unidadempaque->equivalencia : 1);
                $tipo = ($detalle->item->unidadempaque ? $detalle->item->unidadempaque->codigo : $detalle->item->unidadOrden);

                // Formatear el texto del detalle
                $detalleText = sprintf("%-7s %-7s %-6s %-6s %-6s %-1s\n", $referencia, $color, trim($talla), trim($tipo), $cantidad, $total);

                // Imprimir el detalle
                $printer->text($detalleText);

                // Sumar al total
                $totalPaquetes += $cantidad;
                $totalGeneral += $total;

                // Imprimir la descripción del artículo concatenada con categoria
                $printer->text($categoria . ' ' . $descipcion . "\n");
            }
            $printer->text("------------------------------------------\n");
            $totalText = sprintf("Total unidades: %16s %5s\n", $totalPaquetes, $totalGeneral);
            $printer->text($totalText);
            $printer->text("__________________________________________\n\n");


            //IMPRIMIR CODIGO DE BARRAS 1
            $printer->setBarcodeHeight(80);
            $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
            $printer->barcode("{A" . $serie, Printer::BARCODE_CODE128);
            $printer->feed();

            //Info en medio de los codigos de barras

            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
            $printer->text("NUMEROCAJAS:" . $model->numeroCajas . "\n");
            $printer->text("ORIGEN:" . trim($model->bodegaOrigen->codigo) . ' ' . $model->bodegaOrigen->nombre . "\n");
            $printer->text("DESTINO:" . trim($model->bodegaDestino->codigo) . ' ' . $model->bodegaDestino->nombre . "\n");
            $printer->selectPrintMode();
            $heights = array(1, 2, 4, 8, 16, 32);
            $widths = array(1, 2, 3, 4, 5, 6, 7, 8);
            $printer->text("USUARIO:" . $model->usuario->username . "\n\n");


            //IMPRIMIR CODIGO DE BARRAS 2
            $printer->setBarcodeHeight(80);
            $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
            $printer->barcode("{A" . $numero_serie, Printer::BARCODE_CODE128);
            $printer->feed();

        } finally {
            $printer->cut();
            $printer->close();
        }

    }
    /**
     * Deletes an existing Traspasodetalle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $idtraspaso)
    {
        $model = $this->findModel($id, $idtraspaso);
        $model->codigoitem = $model->item->codigoBarras;

        if ($model->cantidad > 1) {
            $model->cantidad--;
            $model->save();
        } else {
            $model->delete();
        }

        return $this->redirect(['/traspasodetalle/create', 'idtraspaso' => $idtraspaso]);
    }

    /**
     * Finds the Traspasodetalle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Traspasodetalle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Traspasodetalle::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }

}
