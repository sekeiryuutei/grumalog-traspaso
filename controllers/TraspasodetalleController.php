<?php

namespace app\controllers;

use app\models\Item;
use app\models\search\TraspasodetalleSearch;
use app\models\Traspaso;
use app\models\Traspasodetalle;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
            // $modelitem = Item::findOne(['id' => $modeltraspaso->idUltimoItem]);
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
        // Obtener la cantidad de elementos asociados al traspaso
        // $count = Traspasodetalle::find()->where(['idTraspaso' => $idtraspaso])->count();

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
                // $modelitem = Item::findOne(['codigoBarras' => $model->codigoitem]);
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

                    // $inventario = $modeldetalle->getInventario($model->codigoitem, '010');
                    // var_dump($inventario);
                    // var_dump($inventario > null . '    -   ');
                    // var_dump($model->codigoitem . '  codigo de bodega  '. $model->traspaso->bodegaOrigen->codigo);
                    // die();

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

                        Yii::$app->session->setFlash('error', 'Articulo sin existencia para traspaso: ' . $model->codigoitem);

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

        // return $this->redirect(['/traspaso/index']);
        $modeldetalles = $model->traspasodetalles;

        //$model = Traspaso::find()->where(['id' => $idtraspaso])->one();
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


        return $this->render('view_recibo', [
            'model' => $model,
            'modeldetalles' => $modeldetalles,
        ]);


        // $content = $this->renderPartial('view_recibo', [
        //     'model' => $model,
        //     'modeldetalles' => $modeldetalles
        // ]);

        // // setup kartik\mpdf\Pdf component
        // $pdf = new Pdf([
        //     // set to use core fonts only
        //     'mode' => Pdf::MODE_CORE,
        //     // A4 paper format
        //     'format' => Pdf::FORMAT_A4,
        //     // portrait orientation
        //     'orientation' => Pdf::ORIENT_PORTRAIT,
        //     // stream to browser inline
        //     'destination' => Pdf::DEST_BROWSER,
        //     //'destination' => Pdf::DEST_DOWNLOAD, 
        //     // your html content input
        //     'content' => $content,
        //     // format content from your own css file if needed or use the
        //     // enhanced bootstrap css built by Krajee for mPDF formatting 
        //     'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
        //     // any css to be embedded if required
        //     'cssInline' => '.kv-heading-1{font-size:10px}',
        //     // set mPDF properties on the fly
        //     'options' => ['title' => 'Traspaso de mercancia'],
        //     // call mPDF methods on the fly
        //     'methods' => [
        //         'SetHeader' => ['Traspaso de mercancia'],
        //         'SetFooter' => ['{PAGENO}'],
        //     ],
        //     'filename' => 'Traspaso-' . $idtraspaso . '.pdf'
        // ]);

        // // return the pdf output as per the destination setting
        // return $pdf->render();

        // return $this->redirect(['/traspaso/index']);
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
