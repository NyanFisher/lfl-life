<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 19.11.2018
 * Time: 16:20
 */

namespace app\controllers;


use app\models\AccountActivation;
use app\models\LoginForm;
use app\models\ResetPasswordForm;
use app\models\SendEmailForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;


class EmailController extends Controller
{
    /*Активация аккаунта через почту*/
    public function actionActivateAccount($key)
    {
        try {
            $user = new AccountActivation($key);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($user->activateAccount()) {
            Yii::$app->session->setFlash('success', 'Активация прошла успешно! <strong>' . Html::encode($user->username) . '<strong> Вы теперь официальный пользователь LFL-Life');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка активации');
            Yii::error('Ошибка при активации');
        }
        return $this->redirect(['login']);
    }

    /*Отправка письма на почту "сброс пароля"*/
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

    /*Сборс пароля*/
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