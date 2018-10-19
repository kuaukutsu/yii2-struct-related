<?php
namespace kuaukutsu\struct\related\storage;

use yii\db\Query;
use kuaukutsu\struct\related\helpers\DbHelper;
use kuaukutsu\struct\related\RelatedItem;
use kuaukutsu\struct\related\StorageInterface;

/**
 * Class DbStorage
 * @package kuaukutsu\struct\related\storage
 *
 * @property string lft_key
 * @property int lft_id
 * @property string rgt_key
 * @property int rgt_id
 * @property int type
 *
 */
class DbStorage extends BaseStorage
{
    /**
     * @var array
     */
    protected $leftKeys = ['lft_id', 'lft_key'];

    /**
     * @var array
     */
    protected $rightKeys = ['rgt_id', 'rgt_key'];

    /**
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%related}}';
    }

    /**
     * @param int|null $type
     * @return Query
     */
    public function find(?int $type = self::TYPE_CONTEXT): Query
    {
        $query = (new Query())
            ->from(static::tableName())
            ->where(['OR',
                $this->model->getRelatedItem()->toPrepareKey($this->leftKeys),
                $this->model->getRelatedItem()->toPrepareKey($this->rightKeys)
            ]);

        if ($type) {
            $query->andWhere(['type' => $type]);
        }

        return $query;
    }

    /**
     * @param int $type
     * @return array
     */
    public function getItems(int $type = self::TYPE_CONTEXT): array
    {
        return $this->find($type)->all(static::getDb());
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     * @throws \yii\db\Exception
     */
    public function attach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        // HAS_ONE
        if ($this->isModeHasOne()) {
            $this->delete($type);
        }

        // exists revert
        if (!$this->find($type)->andWhere($relatedItem->toPrepareKey($this->leftKeys))->exists()) {

            // insert ignore
            DbHelper::insertIgnore(static::tableName(), array_merge(
                $this->model->getRelatedItem()->toPrepareKey($this->leftKeys),
                $relatedItem->toPrepareKey($this->rightKeys),
                ['type' => $type]
            ), static::getDb())
                ->execute();
        }

        return $this;
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     * @throws \yii\db\Exception
     */
    public function detach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        // left
        static::getDb()
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                $this->model->getRelatedItem()->toPrepareKey($this->leftKeys),
                $relatedItem->toPrepareKey($this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

        // right
        static::getDb()
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                $relatedItem->toPrepareKey($this->leftKeys),
                $this->model->getRelatedItem()->toPrepareKey($this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

        return $this;
    }

    /**
     * @param int|null $type
     * @return mixed|void
     * @throws \yii\db\Exception
     */
    public function delete(?int $type = null)
    {
        $condition = $this->model->getRelatedItem()->toPrepareKey($this->leftKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // left
        static::getDb()
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();

        $condition = $this->model->getRelatedItem()->toPrepareKey($this->rightKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // right
        static::getDb()
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();
    }
}