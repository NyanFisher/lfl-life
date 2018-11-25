<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 24.11.2018
 * Time: 0:02
 * @var $user \app\models\User
 */
use yii\helpers\Html;

echo 'Доброго времени суток'.Html::encode($user->username).'.';
echo Html::a('Для активации аккаунта перейдите по этой ссылке: '.
Yii::$app->urlManager->createAbsoluteUrl(
    [
        '/site/activate-account',
        'key'=>$user->secret_key
    ]
));