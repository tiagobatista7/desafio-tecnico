<?php

namespace app\components;

use Yii;
use yii\web\UnauthorizedHttpException;

class JwtAuth extends \yii\filters\auth\AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');

        Yii::info("Header Authorization: " . ($authHeader ?? 'null'), __METHOD__);

        if ($authHeader && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $tokenString = $matches[1];
            Yii::info("Token extraído: " . $tokenString, __METHOD__);

            try {
                $token = $this->decodeToken($tokenString);
                Yii::info("Payload do token decodificado: " . json_encode($token), __METHOD__);

                $userId = $token->uid ?? $token->id ?? $token->user_id ?? null;
                Yii::info("User ID extraído do token: " . ($userId ?? 'null'), __METHOD__);

                if ($userId === null) {
                    throw new UnauthorizedHttpException('Token inválido: User ID não encontrado');
                }

                $identity = \Yii::$app->user->loginByAccessToken($tokenString, get_class($this));

                if ($identity === null) {
                    Yii::warning("Falha no login do usuário pelo token.", __METHOD__);
                    throw new UnauthorizedHttpException('Token inválido: usuário não encontrado');
                }

                Yii::info("Usuário autenticado com sucesso. ID: " . $userId, __METHOD__);

                return $identity;
            } catch (\Exception $e) {
                Yii::error("Erro ao autenticar token: " . $e->getMessage(), __METHOD__);
                throw new UnauthorizedHttpException('Token inválido: ' . $e->getMessage());
            }
        }

        Yii::warning("Header Authorization ausente ou formato inválido.", __METHOD__);
        return null;
    }

    protected function decodeToken($tokenString)
    {
    }
}
