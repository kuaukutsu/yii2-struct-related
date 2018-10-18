<?php
namespace kuaukutsu\struct\related\tests\models;

use yii\base\Model;
use kuaukutsu\struct\related\Related;
use kuaukutsu\struct\related\RelatedItem;
use kuaukutsu\struct\related\ModelInterface;
use kuaukutsu\struct\related\StorageInterface;

/**
 * Class BaseModel
 * @package kuaukutsu\struct\related\tests\models
 */
class BaseModel extends Model implements ModelInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var Related
     */
    protected $related;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();

        $this->related = \Yii::$container->get('structRelated');
    }

    /**
     * @var RelatedItem
     */
    private $_relatedItem;

    /**
     * @return RelatedItem
     */
    public function getRelatedItem(): RelatedItem
    {
        if ($this->_relatedItem === null) {
            $this->_relatedItem = new RelatedItem(['id' => $this->id, 'key' => 'test']);
        }

        return $this->_relatedItem;
    }

    /**
     * @return \kuaukutsu\struct\related\StorageInterface
     */
    public function getRelated(): StorageInterface
    {
        return $this->related->hasMany($this);
    }

    /**
     * @return \kuaukutsu\struct\related\StorageInterface
     */
    public function getRelatedParent(): StorageInterface
    {
        return $this->related->hasOne($this);
    }
}