<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 24.11.2018
 * Time: 0:07
 */

namespace app\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/* @property string $username */

class AccountActivation extends Model
{
    /* @var $user \app\models\User */
    private $_user;
    public function __construct($key, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым.');
        $this->_user = User::findBySecretKey($key);
        if(!$this->_user)
            throw new InvalidParamException('Не верный ключ.');
        parent::__construct($config);
    }

    public function activateAccount()
    {
        $user=$this->_user;
        $user->status=User::STATUS_ACTIVE;
        $user->removeSecretKey();
        return $user->save();
    }
    /*Возвращает ник активированного пользователя*/
    public function getUsername()
    {
        $user=$this->_user;
        return $user->username;
    }
}