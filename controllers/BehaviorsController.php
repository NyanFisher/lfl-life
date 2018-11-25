<?php
/**
 * Created by PhpStorm.
 * User: zaiki
 * Date: 24.11.2018
 * Time: 15:17
 */

namespace app\controllers;


use yii\filters\AccessControl;
use yii\web\Controller;

class BehaviorsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    //---------------------------------------/Site\---------------------------------------------------//
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['login', 'signup'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['logout', 'about','login'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                    ],
                    //---------------------------------------\Site/---------------------------------------------------//

                    //---------------------------------------/Email\--------------------------------------------------//
                    [
                        'allow' => true,
                        'controllers' => ['email'],
                        'actions' => ['send-email', 'reset-password', 'activate-account'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['email'],
                        'actions' => ['reset-password'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                    ],
                    //---------------------------------------\Email/--------------------------------------------------//

                    //---------------------------------------/Profile\------------------------------------------------//
                    [
                        'allow' => true,
                        'controllers' => ['profile'],
                        'actions' => ['profile'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                    ],
                    //---------------------------------------\Profile/------------------------------------------------//

                    [
                        'allow' => true,
                        'actions' => ['index']
                    ],
                ],
            ],
        ];
    }
}