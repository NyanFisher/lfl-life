<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
?>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3><?= $this->title ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-lg-offset-1 col-lg-11">
                                <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                                <a href="<?= \yii\helpers\Url::to(['signup']) ?>"
                                   class="btn btn-success">Регистрация</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <?= Html::a('Забыли пароль?', ['/email/send-email']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
