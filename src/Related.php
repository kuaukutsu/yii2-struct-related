<?php
namespace kuaukutsu\struct\related;

/**
 * Class Related
 * @package kuaukutsu\struct\related
 */
class Related
{
    /**
     * @var StorageInterface
     */
    private $_storage;

    /**
     * Related constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->_storage = $storage;
    }

    /**
     * This mode allows have one value (left OR right)
     *
     * @param ModelInterface $model
     * @return StorageInterface
     */
    public function hasOne(ModelInterface $model): StorageInterface
    {
        return $this->_storage->setInstance($model, StorageInterface::MODE_HAS_ONE);
    }

    /**
     * This mode allows have many value (left AND right)
     *
     * @param ModelInterface $model
     * @return StorageInterface
     */
    public function hasMany(ModelInterface $model): StorageInterface
    {
        return $this->_storage->setInstance($model);
    }
}