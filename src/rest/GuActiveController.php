<?php

namespace gunter1020\yii2\common\rest;

use yii\rest\ActiveController;

/**
 * Extends Yii rest ActiveController class.
 *
 * @author Gunter Chou <abcd2221925@gmail.com>
 */
abstract class GuActiveController extends ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return BehaviorsConfig::corsHttpBearer(parent::behaviors());
    }
}
