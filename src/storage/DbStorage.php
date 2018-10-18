<?php
namespace kuaukutsu\struct\related\storage;

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
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%related}}';
    }

    /**
     * @param int $type
     * @return array
     */
    public function getItems(int $type = self::TYPE_CONTEXT): array
    {
        return [];
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

        \Yii::$app->db
            ->createCommand()
            ->insert(static::tableName(), array_merge(
                self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys),
                self::toRelatedKey($relatedItem, $this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

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
        \Yii::$app->db
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                self::toRelatedKey($this->model->getRelatedItem(), $this->leftKeys),
                self::toRelatedKey($relatedItem, $this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

        // right
        \Yii::$app->db
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
        \Yii::$app->db
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();

        $condition = self::toRelatedKey($this->model->getRelatedItem(), $this->rightKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // right
        \Yii::$app->db
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