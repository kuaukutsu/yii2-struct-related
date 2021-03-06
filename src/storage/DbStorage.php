<?php
namespace kuaukutsu\struct\related\storage;

use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Query;
use yii\di\NotInstantiableException;
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
     * @var array
     */
    private $leftAttr = ['id', 'key'];

    /**
     * @var array
     */
    private $rightAttr = ['relatedId', 'relatedKey'];

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
        $queryLeft = (new Query())
            ->from(static::tableName())
            ->select(array_merge(
                    array_combine($this->leftAttr, $this->leftKeys),
                    array_combine($this->rightAttr, $this->rightKeys),
                    ['type'])
            )
            ->where($this->model->getRelatedItem()->toPrepareKey($this->leftKeys));

        $queryRight = (new Query())
            ->from(static::tableName())
            ->select(array_merge(
                    array_combine($this->leftAttr, $this->rightKeys),
                    array_combine($this->rightAttr, $this->leftKeys),
                    ['type'])
            )
            ->where($this->model->getRelatedItem()->toPrepareKey($this->rightKeys));

        $query = (new Query())
            ->from(['related' => $queryLeft->union($queryRight)]);

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
        return $this->find($type)->all($this->connection);
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function attach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        // HAS_ONE
        if ($this->isModeHasOne()) {
            $this->delete($type);
        }

        // exists revert
        if (!$this->find($type)->andWhere($relatedItem->toPrepareKey($this->leftAttr))->exists()) {

            // insert ignore
            DbHelper::insertIgnore(static::tableName(), array_merge(
                $this->model->getRelatedItem()->toPrepareKey($this->leftKeys),
                $relatedItem->toPrepareKey($this->rightKeys),
                ['type' => $type]
            ), $this->connection)
                ->execute();
        }

        return $this;
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     * @throws Exception
     */
    public function detach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        // left
        $this->connection
            ->createCommand()
            ->delete(static::tableName(), array_merge(
                $this->model->getRelatedItem()->toPrepareKey($this->leftKeys),
                $relatedItem->toPrepareKey($this->rightKeys),
                ['type' => $type]
            ))
            ->execute();

        // right
        $this->connection
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
     * @throws Exception
     */
    public function delete(?int $type = null)
    {
        $condition = $this->model->getRelatedItem()->toPrepareKey($this->leftKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // left
        $this->connection
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();

        $condition = $this->model->getRelatedItem()->toPrepareKey($this->rightKeys);
        if ($type) {
            $condition = array_merge($condition, ['type' => $type]);
        }

        // right
        $this->connection
            ->createCommand()
            ->delete(static::tableName(), $condition)
            ->execute();
    }
}