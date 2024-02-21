<?php

namespace app\controllers;

use app\models\Traspasodetalle;
use app\models\search\TraspasodetalleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

                $modeldetalle->cantidad = $modeldetalle->cantidad + $model->cantidad;
                $modeldetalle->save();

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

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
