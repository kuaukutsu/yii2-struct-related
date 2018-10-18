<?php
namespace kuaukutsu\struct\related;

use yii\base\BaseObject;

/**
 * Interface RelatedItemInterface
 * @package kuaukutsu\struct\related
 */
class RelatedItem extends BaseObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->id,
            $this->key
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(':', $this->toArray());
    }
}