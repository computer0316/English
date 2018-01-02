<?php

namespace app\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;

class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
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

	/*
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
    	echo 'index.php';
	}
		//获取 Model 错误信息中的 第一条，无错误时 返回 null
		private static function getModelError($model) {
		    $errors = $model->getErrors();    //得到所有的错误信息
		    if(!is_array($errors)) return '';
		    $firstError = array_shift($errors);
		    if(!is_array($firstError)) return '';
		    return array_shift($firstError);
		}
	/*
		Register action
	*/
	public function actionRegister(){
		$this->layout = 'login';
		$model = new RegisterForm();
		$post = Yii::$app->request->post();
		if($model->load($post)){
			// 检查手机号是否存在
			if(!User::validateMobile($model->mobile)){
				Yii::$app->session->setFlash('message', "手机号已经存在！");
				return $this->render('register', ['model' => $model]);
			}
			$user = new User();
			$user->mobile	= $model->mobile;
			$user->password	= md5($model->password);
			if($user->register()){
				Yii::$app->session->setFlash('message', "注册成功！");
			}
			else{
				Yii::$app->session->setFlash('message', $this->getModelError($user));
			}
		}
		return $this->render('register', ['model' => $model]);
	}



    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
    	$this->layout = 'login';
		$model	= new LoginForm();
		$post		= Yii::$app->request->post();
		if($model->load($post)){
			if(User::login($model)){
				return $this->redirect(Url::toRoute('site/index'));
			}
			else{
				Yii::$app->session->setFlash('message', "用户名或者密码错。");
			}
		}
		return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
    	Yii::$app->session->set('userid', '');
    	echo 'abc';
    	die();
        return $this->redirect(Url::toRoute('site/index'));
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
}
