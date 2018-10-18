<?php
namespace kuaukutsu\struct\related\storage;

use kuaukutsu\struct\related\RelatedItem;
use kuaukutsu\struct\related\StorageInterface;

/**
 * Class ArrayStorage
 * @package kuaukutsu\struct\related\storage
 *
 * Только для тестов
 *
 */
class ArrayStorage extends BaseStorage
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @param int $type
     * @return array
     */
    public function getItems(int $type = self::TYPE_CONTEXT): array
    {
        return $this->map[self::toRelatedKey($this->model->getRelatedItem(), $type)] ?? [];
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     * @throws \ReflectionException
     */
    public function attach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        if ($this->isModeHasOne()) {
            $this->delete($type);
        }

        if (!$this->exists($this->model->getRelatedItem(), $relatedItem, $type)) {
            $this->map[self::toRelatedKey($this->model->getRelatedItem(), $type)][] = self::toRelatedKey($relatedItem, $type);
        }

        return $this;
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return StorageInterface
     */
    public function detach(RelatedItem $relatedItem, int $type = self::TYPE_CONTEXT): StorageInterface
    {
        $lftKey = self::toRelatedKey($this->model->getRelatedItem(), $type);
        $rgtKey = self::toRelatedKey($relatedItem, $type);

        if (($this->map[$lftKey] ?? false) && ($pos = array_search($rgtKey, $this->map[$lftKey])) !== false) {
            array_splice($this->map[$lftKey] , $pos, 1);
        }

        return $this;
    }

    /**
     * @param int|null $type
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function delete(?int $type = null)
    {
        if ($type === null) {

            // left
            $reflect = new \ReflectionClass(get_called_class());
            foreach ($reflect->getConstants() as $key => $value) {
                if (stripos($key, 'TYPE_') === 0) {
                    unset($this->map[self::toRelatedKey($this->model->getRelatedItem(), $value)]);
                }
            }

            // right
            if (count($this->map)) {
                $aflip = array_flip($this->map);
                $reflect = new \ReflectionClass(get_called_class());
                foreach ($reflect->getConstants() as $key => $value) {
                    if (stripos($key, 'TYPE_') === 0) {
                        unset($aflip[self::toRelatedKey($this->model->getRelatedItem(), $value)]);
                    }
                }

                $this->map = empty($aflip) ? [] : array_flip($aflip);
            }

            return;
        }

        // left
        unset($this->map[self::toRelatedKey($this->model->getRelatedItem(), $type)]);
    }

    /**********************
     * HELPERs
     *********************/

    /**
     * @param RelatedItem $relatedItemLft
     * @param RelatedItem $relatedItemRgt
     * @param int $type
     * @return bool
     */
    protected function exists(RelatedItem $relatedItemLft, RelatedItem $relatedItemRgt, int $type): bool
    {
        $lftKey = self::toRelatedKey($relatedItemLft, $type);
        $rgtKey = self::toRelatedKey($relatedItemRgt, $type);

        if (($this->map[$lftKey] ?? false) && in_array($rgtKey, $this->map[$lftKey])) {
            return true;
        }

        if (($this->map[$rgtKey] ?? false) && in_array($lftKey, $this->map[$rgtKey])) {
            return true;
        }

        return false;
    }

    /**
     * @param RelatedItem $relatedItem
     * @param int $type
     * @return string
     */
    protected static function toRelatedKey(RelatedItem $relatedItem, int $type): string
    {
        return (string)$relatedItem . ':' . $type;
    }
}