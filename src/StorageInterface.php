<?php
namespace kuaukutsu\struct\related;

use yii\db\Query;

/**
 * Interface StorageInterface
 * @package kuaukutsu\struct\related
 */
interface StorageInterface
{
    /**
     * Type
     */
    public const TYPE_CONTEXT  = 1;
    public const TYPE_PARENT   = 1 << 1;
    public const TYPE_CHILD    = 1 << 2;

    /**
     * Mode
     */
    public const MODE_HAS_ONE  = 1;
    public const MODE_HAS_MANY = 1 << 1;

    /**
     * @param ModelInterface $model
     * @param int $mode
     * @return StorageInterface
     */
    public function setInstance(ModelInterface $model, int $mode = self::MODE_HAS_MANY): self;

    /**
     * Model write left, Related write right
     *
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     */
    public function attach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): self;

    /**
     * Delete right keys
     *
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     */
    public function detach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): self;

    /**
     * Delete all right keys
     *
     * @param int|null $type
     * @return mixed
     */
    public function delete(?int $type = null);

    /**
     * @param int|null $type
     * @return Query
     */
    public function find(?int $type = self::TYPE_CONTEXT): Query;

    /**
     * @param int $type
     * @return array
     */
    public function getItems(int $type = self::TYPE_CONTEXT): array;
}