<?php

namespace app\controllers;

use app\models\search\TraspasoSearch;
use app\models\Tipodocumento;
use app\models\Traspaso;
use yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TraspasoController implements the CRUD actions for Traspaso model.
 */
class TraspasoController extends Controller
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
     * Lists all Traspaso models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TraspasoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    /**
     * Displays a single Traspaso model.
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

    public function actionDetalle($id)
    {
        $model = $this->findModel($id);

        if ($model->idEstado != 0) {
            return $this->redirect(['index']);
        }

        // return $this->redirect(['/traspasodetalle/index', 'idtraspaso' => $id]);
        return $this->redirect(['/traspasodetalle/create', 'idtraspaso' => $model->id]);
    }

    /**
     * Creates a new Traspaso model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Traspaso();
        $model->idEstado = 0;
        if ($model->idEstado != 0) {
            return $this->redirect(['index']);
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $tipoDocumento = Tipodocumento::findOne(['id' => $model->bodegaOrigen->tipodocumento->idTipoDocumento]);
                $model->idTipoDocumento = $tipoDocumento->id;
                $model->consecutivo = $tipoDocumento->consecutivoProximo;
                $model->serie = $tipoDocumento->codigo;
                // Incrementar el próximo consecutivo en Tipodocumento
                $tipoDocumento->consecutivoProximo += 1;
                $tipoDocumento->save();

            }
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['/traspasodetalle/create', 'idtraspaso' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Traspaso model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->idEstado != 0) {
            return $this->redirect(['index']);
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['/traspaso/view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionFactura($id)
    {
        $model = $this->findModel($id);
        if ($model->idEstado !== 1 && $model->idEstado !== 2 && $model->idEstado !== 3) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['/traspasodetalle/print', 'idtraspaso' => $model->id]);
    }

    public function actionAnular($id)
    {
        $model = $this->findModel($id);

        if ($model->idEstado !== 1) {
            return $this->redirect(['index']);
        }

        if ($this->request->isPost) {
            $model->idEstado = 2;
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Ups!, ocurrio un problema con : ' . $model);
            }
        }
    }
    /**
     * Deletes an existing Traspaso model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->idEstado != 0) {
            return $this->redirect(['index']);
        }
        if ($this->request->isPost) {
            $model->idEstado = 3;
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Ups!, ocurrio un problema con : ' . $model);
            }
        }
        // $model->delete();


        return $this->redirect(['index']);
    }

    /**
     * Finds the Traspaso model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Traspaso the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Traspaso::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
