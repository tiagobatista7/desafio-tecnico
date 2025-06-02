<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;

class CreateUserController extends Controller
{   
    public function actionIndex($email, $password)
    {
        $user = new User();
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateAccessToken();

        if ($user->save()) {
            echo "Usuário criado com sucesso.\n";
            return ExitCode::OK;
        } else {
            echo "Erro ao criar usuário:\n";
            print_r($user->getErrors());
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
