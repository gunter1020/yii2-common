<?php

namespace gunter1020\yii2\common\rest;

use yii\rest\Controller as RestController;

class Controller extends RestController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors = BehaviorsConfig::corsHttpBearer($behaviors);

        return $behaviors;
    }
}
