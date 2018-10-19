<?php

use yii\db\Migration;

/**
 * Class m181019_145923_add_table_related
 *
 * Sqlite
 */
class m181019_145923_add_table_related extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

            $this->createTable(\kuaukutsu\struct\related\storage\DbStorage::tableName(), [
                'lft_key' => 'char(8) NOT NULL',
                'lft_id' => 'int(11) unsigned NOT NULL',
                'rgt_key' => 'char(8) NOT NULL',
                'rgt_id' => 'int(11) unsigned NOT NULL',
                'type' => 'tinyint(1) unsigned DEFAULT "1"',
                'PRIMARY KEY (`lft_id`, `lft_key`, `rgt_key`, `rgt_id`)',
                'UNIQUE KEY `UI_content_related_right` (`rgt_id`, `rgt_key`, `lft_key`, `lft_id`)'
            ], $tableOptions);
        }

        if ($this->db->driverName === 'sqlite') {
            $this->createTable(\kuaukutsu\struct\related\storage\DbStorage::tableName(), [
                'lft_key' => 'char(8) NOT NULL',
                'lft_id' => 'int(11) NOT NULL',
                'rgt_key' => 'char(8) NOT NULL',
                'rgt_id' => 'int(11) NOT NULL',
                'type' => 'tinyint(1) DEFAULT "1"',
                'PRIMARY KEY (`lft_id`, `lft_key`, `rgt_key`, `rgt_id`)'
            ], $tableOptions);
        }

        return true;
    }

    public function down()
    {
        $this->dropTable(\kuaukutsu\struct\related\storage\DbStorage::tableName());

        return true;
    }
}
