<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 25.11.2018
 * Time: 13:40
 */

namespace app\controllers;


use app\models\profile\Profile;
use Yii;

class ProfileController extends BehaviorsController
{
    public function actionProfile()
    {
        $model = ($model = Profile::findOne(Yii::$app->user->id)) ? $model : new Profile();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->updateProfile()) {
                Yii::$app->session->setFlash('success', 'Профиль изменен');
            } else {
                Yii::$app->session->setFlash('error', 'Профиль не изменен');
                Yii::error('Ошибка записи. Профиль не изменен');
                return $this->refresh();
            }
        }
        return $this->render(
            'profile', [
            'model' => $model
        ]);
    }

}