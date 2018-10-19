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
            ->from(self::tableName())
            ->where(['OR',
                self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys),
                self::toRelatedKey($this->model->getRelatedItem(), $this->rightKeys)
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
        return $this->find($type)->all(self::getDb());
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
        if (!$this->find($type)->andWhere(self::toRelatedKey($relatedItem, $this->leftKeys))->exists()) {

            // insert ignore
            DbHelper::insertIgnore(static::tableName(), array_merge(
                self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys),
                self::toRelatedKey($relatedItem, $this->rightKeys),
                ['type' => $type]
            ), self::getDb())
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
        self::getDb()
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys),
                self::toRelatedKey($relatedItem, $this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

        // right
        self::getDb()
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                self::toRelatedKey($relatedItem, $this->leftKeys),
                self::toRelatedKey($this->model->getRelatedItem(), $this->rightKeys),
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
        $condition = self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // left
        self::getDb()
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();

        $condition = self::toRelatedKey($this->model->getRelatedItem(), $this->rightKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // right
        self::getDb()
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();
    }

    /**********************
     * HELPERs
     *********************/

    /**
     * @param RelatedItem $relatedItem
     * @param array $nameKeys
     * @return array
     */
    protected static function toRelatedKey(RelatedItem $relatedItem, array $nameKeys = []): array
    {
        if ($nameKeys === []) {
            return $relatedItem->toArray();
        }

        return array_combine($nameKeys, $relatedItem->toArray());
    }
}