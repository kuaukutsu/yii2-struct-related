<?php
namespace kuaukutsu\struct\related\tests;

use kuaukutsu\struct\related\RelatedItem;
use kuaukutsu\struct\related\storage\DbStorage;
use kuaukutsu\struct\related\StorageInterface;
use kuaukutsu\struct\related\tests\models\BaseModel;

/**
 * Class DbStorageTest
 * @package kuaukutsu\struct\related\tests
 */
class DbStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \yii\db\Exception
     */
    public function setUp()
    {
        parent::setUp();

        // clear
        \Yii::$app->db->createCommand('DELETE FROM ' . DbStorage::tableName())->execute();
    }

    public function testAttach()
    {
        $modelA = new BaseModel(['id' => 23232]);
        $modelB = new BaseModel(['id' => 45]);

        $modelA->getRelated()
            ->attach($modelB->getRelatedItem())
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]));

        $this->assertEquals(2, $modelA->getRelated()->find()->count());

        $modelB->getRelated()
            ->attach($modelA->getRelatedItem())
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]));

        $this->assertEquals(2, $modelA->getRelated()->find()->count());

        $modelC = new BaseModel(['id' => 10]);
        $modelC->getRelated()
            ->attach($modelA->getRelatedItem());

        $this->assertEquals(3, $modelA->getRelated()->find()->count());

        $this->assertEquals(2, $modelB->getRelated()->find()->count());
    }

    public function testAttachIgnoreUnique()
    {
        $modelA = new BaseModel(['id' => 23232]);
        $modelB = new BaseModel(['id' => 45]);

        $modelA->getRelated()
            ->attach($modelB->getRelatedItem())
            ->attach($modelB->getRelatedItem())
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]));

        $this->assertEquals(2, $modelA->getRelated()->find()->count());
    }

    public function testDetach()
    {
        $modelA = new BaseModel(['id' => 23232]);
        $modelB = new BaseModel(['id' => 45]);

        $modelA->getRelated()
            ->attach($modelB->getRelatedItem())
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]));

        $this->assertEquals(2, $modelA->getRelated()->find()->count());

        $modelA->getRelated()
            ->detach($modelB->getRelatedItem());

        $this->assertEquals(1, $modelA->getRelated()->find()->count());

        $modelA->getRelated()
            ->attach($modelB->getRelatedItem());

        $this->assertEquals(2, $modelA->getRelated()->find()->count());

        $modelB->getRelated()
            ->detach($modelA->getRelatedItem());

        $this->assertEquals(1, $modelA->getRelated()->find()->count());
    }

    public function testDelete()
    {
        $modelA = new BaseModel(['id' => 23232]);

        $modelA->getRelated()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 4]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 5]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 6]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 7]));

        $this->assertEquals(5, $modelA->getRelated()->find()->count());

        $modelA->getRelated()->delete();

        $this->assertEquals(0, $modelA->getRelated()->find()->count());

        $modelA->getRelated()
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 3]))
            ->attach(new RelatedItem(['key' => 'test2', 'id' => 4]), StorageInterface::TYPE_CHILD);

        $this->assertEquals(2, $modelA->getRelated()->find(null)->count());

        $modelA->getRelated()->delete(StorageInterface::TYPE_CHILD);

        $this->assertEquals(1, $modelA->getRelated()->find(null)->count());

        $modelA->getRelated()->delete();

        $this->assertEquals(0, $modelA->getRelated()->find(null)->count());
    }
}