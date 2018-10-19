<?php
return [
    'controllerMap' => [

        'mysql-migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'db' => 'mysql',
            'migrationPath' => dirname(__DIR__) .'/migrations/mysql',
        ],

        'sqlite-migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'db' => 'sqlite',
            'migrationPath' => dirname(__DIR__) .'/migrations/sqlite',
        ],
    ],
];