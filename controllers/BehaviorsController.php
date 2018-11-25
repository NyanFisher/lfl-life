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
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['login', 'signup', 'send-email', 'reset-password', 'activate-account'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['logout', 'reset-password', 'about', 'profile'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index']
                    ],
                ],
            ],
        ];
    }
}