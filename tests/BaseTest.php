<?php
namespace kuaukutsu\struct\related\tests;

use kuaukutsu\struct\related\RelatedItem;
use kuaukutsu\struct\related\StorageInterface;
use kuaukutsu\struct\related\tests\models\BaseModel;

/**
 * Class BaseTest
 * @package kuaukutsu\struct\related\tests
 */
class BaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BaseModel
     */
    private $struct;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        /** @var BaseModel struct */
        $this->struct = new BaseModel(['id' => 23232]);
    }

    /**
     * @test
     */
    public function testAttachMany()
    {
        $this->struct->getRelated()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 2]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]), StorageInterface::TYPE_CHILD)
            ->attach(new RelatedItem(['key' => 'test3', 'id' => 2]));

        $this->assertCount(3, $this->struct->getRelated()->getItems());

        $this->assertCount(1, $this->struct->getRelated()->getItems(StorageInterface::TYPE_CHILD));
    }

    /**
     * @test
     */
    public function testAttachOne()
    {
        $this->struct->getRelatedParent()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 2]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test3', 'id' => 2]));

        $this->assertCount(1, $this->struct->getRelated()->getItems());
    }

    public function testDetach()
    {
        $this->struct->getRelated()->delete();

        $this->struct->getRelated()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 2]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test3', 'id' => 2]));

        $this->assertCount(3, $this->struct->getRelated()->getItems());

        $this->struct->getRelated()
            ->detach(new RelatedItem(['key' => 'test3', 'id' => 2]));

        $this->assertCount(2, $this->struct->getRelated()->getItems());

        $this->struct->getRelated()
            ->detach(new RelatedItem(['key' => 'test2', 'id' => 2]), StorageInterface::TYPE_CHILD);

        $this->assertCount(2, $this->struct->getRelated()->getItems());
    }

    public function testClean()
    {
        $this->struct->getRelated()->delete();

        $this->struct->getRelated()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 2]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test3', 'id' => 2]), StorageInterface::TYPE_CHILD);

        $this->assertCount(2, $this->struct->getRelated()->getItems());

        $this->struct->getRelated()->delete(StorageInterface::TYPE_CONTEXT);

        $this->assertCount(0, $this->struct->getRelated()->getItems());

        $this->assertCount(1, $this->struct->getRelated()->getItems(StorageInterface::TYPE_CHILD));

        $this->struct->getRelated()->delete();

        $this->assertCount(0, $this->struct->getRelated()->getItems(StorageInterface::TYPE_CHILD));
    }
}