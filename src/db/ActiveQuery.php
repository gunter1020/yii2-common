<?php

namespace gunter1020\yii2\common\db;

use gunter1020\yii2\common\db\ActiveRecord;
use yii\db\ActiveQuery as DbActiveQuery;

/**
 * Extends Yii db ActiveQuery class.
 *
 * @property ActiveRecord $modelClass
 */
class ActiveQuery extends DbActiveQuery
{
    /**
     * Filter valid data
     *
     * @param  bool     $status
     * @return static
     */
    public function valid($status = true)
    {
        $class = $this->modelClass;

        return $status
            ? $this->andWhere([$class::SOFT_DELETE => $class::SD_VALID])
            : $this->andWhere([$class::SOFT_DELETE => $class::SD_INVALID]);
    }

    /**
     * Filter invalid data
     *
     * @return static
     */
    public function invalid()
    {
        return $this->valid(false);
    }

    /**
     * Returns a single row of result.
     *
     * @param  Connection|null $db
     * @return array|null
     */
    public function oneArray($db = null)
    {
        return $this->asArray()->one($db);
    }

    /**
     * Returns a multiple row of result.
     *
     * @param  Connection|null $db
     * @return array|null
     */
    public function allArray($db = null)
    {
        return $this->asArray()->all($db);
    }
}
