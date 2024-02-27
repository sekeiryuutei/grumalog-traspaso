<?php

namespace app\controllers;

use app\models\Traspaso;
use app\models\Traspasodetalle;
use app\models\search\TraspasodetalleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Item;
use kartik\mpdf\Pdf;

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
        $model = new Traspasodetalle();
        $model->idTraspaso = $idtraspaso;
        $model->cantidad = 1;

        $model->bodegaorigen = $model->traspaso->bodegaOrigen->nombre;
        $model->bodegadestino = $model->traspaso->bodegaDestino->nombre;

        $searchModel = new TraspasodetalleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $idtraspaso);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $modelitem = Item::findOne(['item' => $model->codigoitem]);

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

                $modeldetalle->codigoitem = $model->codigoitem;

                $modeldetalle->cantidad = $modeldetalle->cantidad + $model->cantidad;
                $modeldetalle->save();

                // var_dump( $modeldetalle->cantidad .' - '. $model->cantidad);die();


                return $this->redirect(['create', 'idtraspaso' => $idtraspaso]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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

        $model->idEstado = 0;
        $model->save();

        return $this->redirect(['/traspaso/index']);
    }

    public function actionPrint($idtraspaso)
    {
        $model = Traspaso::findOne(['id' => $idtraspaso]);

        $modeldetalles = $model->traspasodetalles;


        return $this->render('view_recibo', [
            'model' => $model,
            'modeldetalles' => $modeldetalles,
        ]);
        die();
        // $content =  $this->renderPartial('view_recibo', [
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
        //         'SetHeader'=>['Traspaso de mercancia'], 
        //         'SetFooter'=>['{PAGENO}'],
        //     ],
        //     'filename' => 'Traspaso-' . $idtraspaso. '.pdf'
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
        $this->findModel($id, $idtraspaso)->delete();
        return $this->redirect(['traspasodetalle/index', 'idtraspaso' => $idtraspaso]);
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
