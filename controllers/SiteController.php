<?php

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\Usertraspaso;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use app\models\search\TraspasoSearch;
use app\models\Traspaso;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelusertraspaso = Usertraspaso::findOne(['idUser' => Yii::$app->user->id]);

        if (!Yii::$app->user->isGuest) {
            if (!$modelusertraspaso) {
                Yii::$app->session->setFlash('error', 'Usuario No Autorizado Para Traspaso');
            } else {
                Yii::$app->session->setFlash('success', $modelusertraspaso->empleadoLogistica->empleado->nombreEmpleado);

                return $this->redirect(['/traspaso/index']);
            }
        }

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }




    public function actionMuelle()
    {
        $traspaso = new Traspaso();

        if (Yii::$app->request->isPost) {
            $serie = Yii::$app->request->post('Traspaso')['serie'];
            $consecutivo = Yii::$app->request->post('Traspaso')['consecutivo'];

            return $this->redirect(['cambio-estado', 'id_serie' => $serie, 'id_consecutivo' => $consecutivo]);
        }

        return $this->render('muelle', [
            'model' => $traspaso,
        ]);
    }

    public function actionCambioEstado($id_serie, $id_consecutivo)
    {

        $traspaso = Traspaso::find()
            ->select(['traspaso.*', 'td.codigo']) // Cambio de 'tipodocumento.codigo' a 'td.codigo'
            ->innerJoin('bodegatipodocumento bt', 'bt.idBodega = traspaso.idBodegaOrigen')
            ->innerJoin('tipodocumento td', 'td.id = bt.idTipoDocumento')
            ->where(['traspaso.consecutivo' => $id_consecutivo])
            ->andWhere(['td.codigo' => $id_serie])
            ->andWhere(['traspaso.idEstado' => 1])
            ->one();

        if (!$traspaso) {

            Yii::$app->session->setFlash('warning', 'No se encontró ningún traspaso con la serie y el consecutivo proporcionados en estado "sin enviar".');

            return $this->redirect(['muelle']);

        } else {
            $traspaso->idEstado = 3;
            $traspaso->save();
            Yii::$app->session->setFlash('success', 'Estado de ' . $id_serie . ' '
                . $id_consecutivo . '  cambiado a muelle correctamente.');
            return $this->redirect(['muelle']);
        }
    }


}
