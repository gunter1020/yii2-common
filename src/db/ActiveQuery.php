<?php

namespace gunter1020\yii2\common\db;

use gunter1020\yii2\common\db\ActiveRecord;
use yii\db\ActiveQuery as DbActiveQuery;

class ActiveQuery extends DbActiveQuery
{
    /**
     * Filter valid data
     *
     * @return static
     */
    public function valid()
    {
        return $this->andWhere([ActiveRecord::SOFT_DELETE => ActiveRecord::IS_VALID]);
    }

    /**
     * Filter invalid data
     *
     * @return static
     */
    public function invalid()
    {
        return $this->andWhere([ActiveRecord::SOFT_DELETE => ActiveRecord::IS_INVALID]);
    }

    /**
     * Returns a single row of result.
     *
     * @return array|null
     */
    public function oneArray()
    {
        return $this->asArray()->one();
    }

    /**
     * Returns a multiple row of result.
     *
     * @return array|null
     */
    public function allArray()
    {
        return $this->asArray()->all();
    }
}
