<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';

new yii\web\Application($config);

$email = 'admin@admin.com';
$senhaNova = 'admin123';

$user = app\models\User::findOne(['email' => $email]);

if (!$user) {
    echo "Usuário {$email} não encontrado.\n";
    exit(1);
}

$user->password_hash = \Yii::$app->security->generatePasswordHash($senhaNova);
if ($user->save()) {
    echo "Senha atualizada com sucesso para '{$senhaNova}'.\n";
} else {
    echo "Falha ao atualizar a senha.\n";
    print_r($user->getErrors());
}
