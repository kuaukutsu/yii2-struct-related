<?php

use yii\console\controllers\MigrateController;

return [
    'controllerMap' => [

        'mysql-migrate' => [
            'class' => MigrateController::class,
            'db' => 'mysql',
            'migrationPath' => dirname(__DIR__) .'/migrations/mysql',
        ],

        'sqlite-migrate' => [
            'class' => MigrateController::class,
            'db' => 'sqlite',
            'migrationPath' => dirname(__DIR__) .'/migrations/sqlite',
        ],
    ],
];