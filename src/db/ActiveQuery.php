<?php

namespace gunter1020\yii2\common\db;

use gunter1020\yii2\common\db\ActiveRecord;
use yii\db\ActiveQuery as DbActiveQuery;

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
        return $status
            ? $this->andWhere([ActiveRecord::SOFT_DELETE => ActiveRecord::IS_VALID])
            : $this->andWhere([ActiveRecord::SOFT_DELETE => ActiveRecord::IS_INVALID]);
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
