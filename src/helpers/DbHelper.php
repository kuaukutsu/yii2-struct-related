<?php
namespace kuaukutsu\struct\related\helpers;

use Yii;
use yii\db\Command;
use yii\db\Connection;

/**
 * Class DbHelper
 * @package kuaukutsu\struct\related\helpers
 */
class DbHelper
{
    /**
     * @param string $table
     * @param array $columns
     * @param null|Connection $db
     * @return Command
     */
    public static function insertIgnore(string $table, array $columns, Connection $db=null): Command
    {
        /** @var Connection $connection */
        $connection = $db ?? Yii::$app->db;

        $params = [];
        $sql = $connection->getQueryBuilder()->insert($table, $columns, $params);

        /**
         * Replace `INSERT INTO` on $replace
         */
        $replace = 'INSERT IGNORE INTO';
        if ($connection->driverName === 'sqlite') {
            $replace = 'INSERT OR IGNORE INTO';
        }

        return $connection->createCommand(str_replace('INSERT INTO', $replace, $sql), $params);
    }
}