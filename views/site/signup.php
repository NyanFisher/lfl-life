<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Регистрация';
?>
<?= Html::csrfMetaTags() ?>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3><?= $this->title ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Введите Username') ?>

                <?= $form->field($model, 'email')->textInput()->label('Введите E-mail') ?>

                <?= $form->field($model, 'password')->passwordInput()->label('Введите Пароль') ?>

                <?php
                if ($model->scenario==='emailActivation'):
                    ?>
                    <i>*На указанную электронную почту будет отправлено письмо для активации аккаунта.</i>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-offset-1 col-lg-11">
                                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="<?= \yii\helpers\Url::to(['login']) ?>" class="btn btn-link">Авторизация</a>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>
    <div class="col-md-4"></div>

</div>
