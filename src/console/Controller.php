<?php

namespace gunter1020\yii2\common\console;

use Yii;
use yii\console\Controller as ConsoleController;

class Controller extends ConsoleController
{
    /**
     * 身份轉換
     *
     * @param array $idConfig Identity data
     * @return void
     */
    protected static function setIdentity(array $idConfig)
    {
        $user = Yii::$app->get('user');

        $identityClass = $user->identityClass;

        $user->setIdentity(new $identityClass($idConfig));
    }
}
