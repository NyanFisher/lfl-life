<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $firstName
 * @property string $lastName
 * @property int $leadingFoot
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $secret_key
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['leadingFoot', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'firstName', 'lastName', 'auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['secret_key','unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'leadingFoot' => 'Leading Foot',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',   //Время добавления нового пользователя
            'updated_at' => 'Updated At',   //Время изменения пользователя
        ];
    }

    //Поведение//
    //----------------------------------------------------------------------------------------------------------------//
    /*Автоматически заполняет значения текущего времени для полей created_at и updated_at*/
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    //----------------------------------------------------------------------------------------------------------------//

    //Поиск//
    //----------------------------------------------------------------------------------------------------------------//
    public function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /*Проверка метода isSecretKeyExpire() и если true, то достаем из базы объект с пользователя с ключом переданным*/
    public static function findBySecretKey($key)
    {
        if(!static::isSecretKeyExpire($key))
            return null;
        return static::findOne(
            [
                'secret_key'=>$key
            ]
        );
    }
    //----------------------------------------------------------------------------------------------------------------//

    //Хелперы//
    //----------------------------------------------------------------------------------------------------------------//

    /*Создание из случайной строки и текущего времени одну строку*/
    public function generateSecretKey()
    {
        $this->secret_key=Yii::$app->security->generateRandomString().'_'.time();
    }
    /*Присваивает null свойству secret_key*/
    public function removeSecretKey()
    {
        $this->secret_key=null;
    }

    /*Проверяет является ли пустым полученный ключ и время его действия не истекло*/
    public static function isSecretKeyExpire($key)
    {
        if(empty($key))
            return false;
        //Срок действия секретного ключа
        $expire = Yii::$app->params['secretKeyExpire'];
        // Разбиваем строку на массив("_" - разделитель), где первый элемент будет сгенерированный ранее ключ
        // второй элемент бдует временем создания ключа
        $parts = explode('_',$key);
        //Помещаем в переменную последний элемент массива, т.е. время создания ключа
        $timestamp = (int)end($parts);
        //Складываем время создания ключа и время действия ключа, и если полученное хначение больше
        //либо равно текущему времени, возвращает true, иначе, срок действия ключа истёк
        return $timestamp+$expire>=time();
    }

    /*Генерирует хэш из введённого пароля и присваевает полученное значение полю password_hash(RegForm)*/
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /*Генерирует случайную строку и присваивает значение auth_key. Требуется для чекбокса "Запомнить меня"*/
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /*Проверяет валидацию введенного пароля(LogForm)*/
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    //----------------------------------------------------------------------------------------------------------------//

    //Аутентификация//
    //----------------------------------------------------------------------------------------------------------------//
    /*Находит пользователя по id и по Статусу активированного пользователя*/
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /*Возвращает значение поля auth_key для текущего пользователя*/
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /*Сравнивает полученный ключ с полем auth_user из таблицы для текущего пользователя*/
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
    //----------------------------------------------------------------------------------------------------------------//
}
