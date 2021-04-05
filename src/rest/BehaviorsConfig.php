<?php

namespace gunter1020\yii2\common\rest;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\Response;

class BehaviorsConfig
{
    /**
     * @param  $behaviors
     * @return array
     */
    public static function corsHttpBearer($behaviors)
    {
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];
        // add header auth token verification
        $behaviors['authenticator']['authMethods'][] = HttpBearerAuth::class;

        // only response json
        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Response::FORMAT_JSON,
        ];

        return $behaviors;
    }
}
