<?php

namespace kuaukutsu\struct\related\storage;

use Yii;
use yii\base\BaseObject;
use yii\db\Connection;
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
     * @var Connection
     */
    protected $connection;

    /**
     *
     */
    public function init(): void
    {
        parent::init();
        $this->connection = Yii::$app->db;
    }

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
        return (int)$this->mode === self::MODE_HAS_ONE;
    }
}