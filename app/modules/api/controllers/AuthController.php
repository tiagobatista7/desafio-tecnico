<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\filters\auth\CompositeAuth;
use app\components\JwtAuth;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [JwtAuth::class],
            'except' => ['login'],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        $email = $request->post('email');
        $password = $request->post('password');

        $user = User::findByEmail($email);

        if (!$user || !$user->validatePassword($password)) {
            return [
                'success' => false,
                'message' => 'Email ou senha inválidos.'
            ];
        }

        $secretKey = Yii::$app->params['jwtSecretKey'];

        $payload = [
            'iss' => Yii::$app->request->hostInfo,
            'aud' => Yii::$app->request->hostInfo,
            'iat' => time(),
            'exp' => time() + 3600,
            'uid' => $user->id,
            'email' => $user->email
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return [
            'success' => true,
            'token' => $jwt,
            'user' => [
                'id' => $user->id,
                'email' => $user->email
            ]
        ];
    }
}
