<?php

namespace gunter1020\yii2\common\rest;

use yii\rest\ActiveController as RestActiveController;

class ActiveController extends RestActiveController
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
