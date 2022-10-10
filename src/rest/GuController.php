<?php

namespace gunter1020\yii2\common\rest;

use yii\rest\Controller;
use yii\rest\OptionsAction;

/**
 * Extends Yii rest Controller class.
 *
 * @author Gunter Chou <abcd2221925@gmail.com>
 */
abstract class GuController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return BehaviorsConfig::corsHttpBearer(parent::behaviors());
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }
}
