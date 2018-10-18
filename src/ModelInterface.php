<?php
namespace kuaukutsu\struct\related;

/**
 * Interface ModelInterface
 * @package kuaukutsu\struct\related
 */
interface ModelInterface
{
    /**
     * @return RelatedItem
     */
    public function getRelatedItem(): RelatedItem;
}