<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 19.11.2018
 * Time: 16:20
 */

namespace app\controllers;


use app\models\AccountActivation;
use app\models\ResetPasswordForm;
use app\models\SendEmailForm;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;


class EmailController extends BehaviorsController
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
        return $this->redirect(['/site/login']);
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

        return $this->render('/email/sendEmail', [
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
                return $this->redirect(['/site/login']);
            }
        }
        return $this->render('/email/resetPassword', [
            'model' => $model,
        ]);
    }

}