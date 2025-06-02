<?php

namespace app\components;

use sizeg\jwt\components\JwtValidationData as BaseJwtValidationData;

class CustomJwtValidationData extends BaseJwtValidationData
{
    public $currentUserId;

    public function init()
    {
        parent::init();

        $this->currentUserId = (\Yii::$app->has('user') && !\Yii::$app->user->isGuest)
            ? \Yii::$app->user->id
            : null;
    }
}
