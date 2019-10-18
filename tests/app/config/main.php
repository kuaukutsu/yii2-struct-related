<?php

use yii\db\Connection;
use kuaukutsu\struct\related\StorageInterface;
use kuaukutsu\struct\related\Related;
use kuaukutsu\struct\related\storage\DbStorage;

return [
    'id' => 'yii2-struct-related',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'runtimePath' => dirname(__DIR__, 2) . '/runtime',

    'container' => [
        'definitions' => [
            StorageInterface::class => [
                'class' => DbStorage::class
            ]
        ],
        'singletons' => [
            'structRelated' => [
                'class' => Related::class
            ]
        ],
    ],

    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'sqlite:' . dirname(__DIR__) .'/data/sqllite.db',
        ]
    ]
];