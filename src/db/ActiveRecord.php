<?php

namespace gunter1020\yii2\common\db;

use Ramsey\Uuid\Uuid;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord as DbActiveRecord;

abstract class ActiveRecord extends DbActiveRecord
{
    /**
     * @var string|null field for uuid key
     */
    protected ?string $uuidAttribute = 'uuid';

    /**
     * @var string|null auto field for created time
     */
    protected ?string $createdAtAttribute = 'created_at';

    /**
     * @var string|null auto field for created user
     */
    protected ?string $createdByAttribute = 'created_by';

    /**
     * @var string|null auto field for updated time
     */
    protected ?string $updatedAtAttribute = 'updated_at';

    /**
     * @var string|null auto field for updated user
     */
    protected ?string $updatedByAttribute = 'updated_by';

    /**
     * @var string|null auto field for deleted time (active at soft-delete isset)
     */
    protected ?string $deletedAtAttribute = 'deleted_at';

    /**
     * @var string|null auto field for deleted user (active at soft-delete isset)
     */
    protected ?string $deletedByAttribute = 'deleted_by';

    /**
     * @var bool whether to perform soft delete instead of regular delete.
     */
    protected bool $replaceRegularDelete = false;

    /**
     * @var string|null auto field for soft-delete
     */
    const SOFT_DELETE = 'is_deleted';

    /**
     * @var mixed soft-delete valid value
     */
    const SD_VALID = false;

    /**
     * @var mixed soft-delete invalid value
     */
    const SD_INVALID = true;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // uuid
        if ($this->uuidAttribute) {
            $behaviors['presetCols'] = [
                'class' => AttributesBehavior::class,
                'attributes' => [
                    $this->uuidAttribute => [
                        ActiveRecord::EVENT_BEFORE_INSERT => static::generatePkId(),
                    ],
                ],
            ];
        }

        // timestamp column attributes
        $timestampAttributes = [];

        // created at
        if ($this->createdAtAttribute) {
            $timestampAttributes[ActiveRecord::EVENT_BEFORE_INSERT][] = $this->createdAtAttribute;
        }

        // updated at
        if ($this->updatedAtAttribute) {
            $timestampAttributes[ActiveRecord::EVENT_BEFORE_INSERT][] = $this->updatedAtAttribute;
            $timestampAttributes[ActiveRecord::EVENT_BEFORE_UPDATE][] = $this->updatedAtAttribute;
        }

        // timestamp columns behavior
        if (count($timestampAttributes)) {
            $behaviors['timestampCols'] = [
                'class' => TimestampBehavior::class,
                'attributes' => $timestampAttributes,
                'value' => function () {
                    return Yii::$app->get('formatter')->asDatetime(time());
                },
            ];
        }

        // blameable column attributes
        $blameableAttributes = [];

        // created by
        if ($this->createdByAttribute) {
            $blameableAttributes[ActiveRecord::EVENT_BEFORE_INSERT][] = $this->createdByAttribute;
        }

        // updated by
        if ($this->updatedByAttribute) {
            $blameableAttributes[ActiveRecord::EVENT_BEFORE_INSERT][] = $this->updatedByAttribute;
            $blameableAttributes[ActiveRecord::EVENT_BEFORE_UPDATE][] = $this->updatedByAttribute;
        }

        // blameable columns behavior
        if (count($blameableAttributes)) {
            $behaviors['blameableCols'] = [
                'class' => BlameableBehavior::class,
                'attributes' => $blameableAttributes,
            ];
        }

        // is delete
        if (static::SOFT_DELETE) {
            // soft-delete behavior
            $behaviors['softDeleteAndRestore'] = [
                'class' => SoftDeleteBehavior::class,
                'replaceRegularDelete' => $this->replaceRegularDelete,
                'softDeleteAttributeValues' => [
                    static::SOFT_DELETE => static::SD_INVALID,
                ],
                'restoreAttributeValues' => [
                    static::SOFT_DELETE => static::SD_VALID,
                ],
            ];

            // deleted at
            if ($this->deletedAtAttribute) {
                $behaviors['softDeleteAndRestore']['softDeleteAttributeValues'][$this->deletedAtAttribute] = function () {
                    return Yii::$app->get('formatter')->asDatetime(time());
                };
            }

            // deleted by
            if ($this->deletedByAttribute) {
                $behaviors['softDeleteAndRestore']['softDeleteAttributeValues'][$this->deletedByAttribute] = function () {
                    return Yii::$app->has('user') ? Yii::$app->get('user')->getId() : null;
                };
            }
        }

        return $behaviors;
    }

    /**
     * @see \yii2tech\ar\softdelete\SoftDeleteBehavior
     *
     * @throws InvalidConfigException
     * @return int|false
     */
    public function softDelete()
    {
        $behavior = $this->getBehavior('softDeleteAndRestore');
        return ($behavior instanceof Behavior) ? $behavior->softDelete() : false;
    }

    /**
     * {@inheritDoc}
     * @see https://github.com/yii2tech/ar-softdelete/issues/12
     * @see \yii2tech\ar\softdelete\SoftDeleteBehavior beforeDelete()
     */
    protected function deleteInternal()
    {
        if ($this->replaceRegularDelete) {
            $this->beforeDelete();
            return true;
        } else {
            return parent::deleteInternal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMany($class, $link)
    {
        return $class::SOFT_DELETE
            ? parent::hasMany($class, $link)->andWhere([$class::SOFT_DELETE => $class::SD_VALID])
            : parent::hasMany($class, $link);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOne($class, $link)
    {
        return $class::SOFT_DELETE
            ? parent::hasOne($class, $link)->andWhere([$class::SOFT_DELETE => $class::SD_VALID])
            : parent::hasOne($class, $link);
    }

    /**
     * Generate primary key id
     *
     * @return string
     */
    protected static function generatePkId(): string
    {
        return Uuid::uuid1()->toString();
    }
}
