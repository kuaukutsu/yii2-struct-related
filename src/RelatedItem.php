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

    /**********************
     * HELPERs
     *********************/

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
     * @param array $nameKeys
     * @return array
     */
    public function toPrepareKey(array $nameKeys = []): array
    {
        if ($nameKeys === []) {
            return $this->toArray();
        }

        return array_combine($nameKeys, $this->toArray());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(':', $this->toArray());
    }
}