<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php'; 

new yii\web\Application(require __DIR__ . '/config/web.php');

$senha = $argv[1] ?? null;

if (!$senha) {
    echo "Uso: php generate-hash.php <senha>\n";
    exit(1);
}

$hash = Yii::$app->security->generatePasswordHash($senha);
echo "Hash gerado:\n$hash\n";
