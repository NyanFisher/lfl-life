<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 21.11.2018
 * Time: 23:41
 */

namespace app\models;


use yii\base\InvalidParamException;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    private $_user;

    public function rules()
    {
        return [
            ['password', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function __construct($key, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым.');
        $this->_user = User::findBySecretKey($key);
        if(!$this->_user)
            throw new InvalidParamException('Не верный ключ.');
        parent::__construct($config);
    }

    public function resetPassword()
    {
        /* @var $user User */ //указываем, что $user это объект модели User
        $user = $this->_user;
        $user->setPassword($this->password); //Устанавливаем в свойство password_hash хэш введенного нового пароля
        $user->removeSecretKey();            //secret_key=null
        return $user->save();
    }
}