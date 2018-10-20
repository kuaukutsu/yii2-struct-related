<?php
namespace kuaukutsu\struct\related\storage;

use yii\base\BaseObject;
use kuaukutsu\struct\related\ModelInterface;
use kuaukutsu\struct\related\StorageInterface;

/**
 * Class BaseStorage
 * @package kuaukutsu\struct\related
 */
abstract class BaseStorage extends BaseObject implements StorageInterface
{
    /**
     * @var ModelInterface
     */
    protected $model;

    /**
     * @var int
     */
    protected $mode = self::MODE_HAS_MANY;

    /**
     * @param ModelInterface $model
     * @param int $mode
     * @return BaseStorage
     */
    public function setInstance(ModelInterface $model, int $mode = self::MODE_HAS_MANY): StorageInterface
    {
        $this->mode = $mode;
        $this->model = $model;
        return $this;
    }

    /**
     * @return bool
     */
    protected function isModeHasOne(): bool
    {
        return $this->mode == self::MODE_HAS_ONE;
    }
}