<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 19.11.2018
 * Time: 16:59
 */

namespace app\models;


use yii\base\Model;
use yii\helpers\Html;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password'], 'required', 'message' => 'Поле не может быть пустым'],
            ['username', 'string', 'length' => [4, 32]],
            ['password', 'string', 'length' => [6, 255]],
            ['username', 'unique',
                'targetClass' => 'app\models\User',
                'message' => 'Логин уже занят'
            ],
            ['email', 'email', 'message' => 'Не верный формат почты'],
            ['email', 'unique',
                'targetClass' => 'app\models\User',
                'message' => 'Адрес уже зарегестрирован'
            ],
            ['status','default','value'=>User::STATUS_ACTIVE,'on'=>'default'],
            ['status','in','range'=>[
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE
            ]]
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Эл. Почта',
            'password' => 'Пароль',
        ];
    }

    public function signup()
    {

        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = Html::encode($this->username);
        $user->email = Html::encode($this->email);
        $user->status=$this->status;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }
}