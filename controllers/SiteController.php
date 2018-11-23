<?php

namespace app\controllers;


use app\models\AccountActivation;
use http\Url;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\ResetPasswordForm;
use app\models\SendEmailForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout', 'send-email', 'reset-password','activate-account'],
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
        return $this->render('index');
    }

    /* Регистрация*/

    public function actionSignup()
    {
        $emailActivation = Yii::$app->params['emailActivation'];
        $model = $emailActivation ? new SignUpForm(['scenario' => 'emailActivation']) : new SignUpForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()):
            /* Из signup() возвращается либо объект пользователя(если он сохранился в БД), или null*/
            if ($user = $model->signup()):
                /* Если signup() вернул объект сохраненного пользователя проверем чтобы его статус был активированным*/
                if ($user->status === User::STATUS_ACTIVE):
                    /* Если статус активированный, проводим аутентификацию пользователя login()*/
                    if (Yii::$app->getUser()->login($user)):
                        return $this->goHome();
                    endif;
                else:
                    if ($model->sendActivationEmail($user)):
                        Yii::$app->session->setFlash('success', 'Письмо отправлено на email <strong>' . Html::encode($user->email) . '</strong>( Проверьте папку спам)');
                    else:
                        Yii::$app->session->setFlash('error', 'Ошибка. Письма не отправлено');
                        Yii::error('Ошибка отправки письма');
                    endif;
                    return $this->refresh(); //обновляем представление signup
                endif;
            else:
                Yii::$app->session->setFlash('error', 'Взникла ошибка при регистрации');
                Yii::error('Ошибка при регистрации');
                return $this->refresh();
            endif;
        endif;
        return $this->render('signup', ['model' => $model]);
    }

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


    public function actionActivateAccount($key)
    {
        try{
            $user=new AccountActivation($key);
        }
        catch(InvalidParamException $e)
        {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($user->activateAccount())
        {
            Yii::$app->session->setFlash('success','Активация прошла успешно! <strong>'.Html::encode($user->username).'<strong> Вы теперь официальный пользователь LFL-Life');
        }
        else
        {
            Yii::$app->session->setFlash('error','Ошибка активации');
            Yii::error('Ошибка при активации');
        }
        return $this->redirect(['login']);
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

    public function actionSendEmail()
    {
        $model = new SendEmailForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->sendEmail()) {
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте email');
                    return $this->goHome();
                } else
                    Yii::$app->getSession()->setFlash('error', 'Нельзя сбросить пароль');

            }
        }

        return $this->render('sendEmail', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($key)
    {
        try {
            $model = new ResetPasswordForm($key);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->resetPassword()) {
                Yii::$app->getSession()->setFlash('warning', 'Пароль изменен.');
                return $this->redirect(['login']);
            }
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
