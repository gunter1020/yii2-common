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

class ActiveRecord extends DbActiveRecord
{
    /**
     * @var string|null field for uuid key
     */
    protected $uuidAttribute = 'uuid';

    /**
     * @var string|null auto field for created time
     */
    protected $createdAtAttribute = 'created_at';

    /**
     * @var string|null auto field for created user
     */
    protected $createdByAttribute = 'created_by';

    /**
     * @var string|null auto field for updated time
     */
    protected $updatedAtAttribute = 'updated_at';

    /**
     * @var string|null auto field for updated user
     */
    protected $updatedByAttribute = 'updated_by';

    /**
     * @var string|null auto field for deleted time (active at soft-delete isset)
     */
    protected $deletedAtAttribute = 'deleted_at';

    /**
     * @var string|null auto field for deleted user (active at soft-delete isset)
     */
    protected $deletedByAttribute = 'deleted_by';

    /**
     * @var bool whether to perform soft delete instead of regular delete.
     */
    protected $replaceRegularDelete = false;

    /**
     * @var string|null auto field for soft-delete
     */
    const SOFT_DELETE = 'is_deleted';

    /**
     * @var string soft-delete valid value
     */
    const IS_VALID = false;

    /**
     * @var string soft-delete invalid value
     */
    const IS_INVALID = true;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // uuid
        if ($this->uuidAttribute) {
            $behaviors['presetCols'] = [
                'class' => AttributesBehavior::class,
                'attributes' => [
                    $this->uuidAttribute => [
                        ActiveRecord::EVENT_BEFORE_INSERT => function () {
                            return Uuid::uuid1();
                        },
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
        if (self::SOFT_DELETE) {
            // soft-delete behavior
            $behaviors['softDeleteAndRestore'] = [
                'class' => SoftDeleteBehavior::class,
                'replaceRegularDelete' => $this->replaceRegularDelete,
                'softDeleteAttributeValues' => [
                    self::SOFT_DELETE => self::IS_INVALID,
                ],
                'restoreAttributeValues' => [
                    self::SOFT_DELETE => self::IS_VALID,
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
     * {@inheritdoc}
     */
    public function rules()
    {
        $rule = parent::rules();

        if (self::SOFT_DELETE) {
            $rule[] = [[self::SOFT_DELETE], 'in', 'range' => [self::IS_INVALID, self::IS_VALID]];
        }

        return $rule;
    }

    /**
     * @see vendor\yii2tech\ar-softdelete\src\SoftDeleteBehavior.php
     *
     * @throws InvalidConfigException
     * @return int|false
     */
    public function softDelete()
    {
        $behavior = $this->getBehavior('softDeleteAndRestore');
        return ($behavior instanceof Behavior) ? $behavior->softDelete() : false;
    }
}
