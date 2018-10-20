<?php
namespace kuaukutsu\struct\related;

/**
 * Class RelatedDTO
 * @package kuaukutsu\struct\related
 */
class RelatedDTO
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
     * @var int
     */
    public $relatedId;

    /**
     * @var string
     */
    public $relatedKey;

    /**
     * @var int
     */
    public $type;

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getRelatedId(): int
    {
        return (int)$this->relatedId;
    }

    /**
     * @return string
     */
    public function getRelatedKey(): string
    {
        return $this->relatedKey;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return (int)$this->type;
    }
}