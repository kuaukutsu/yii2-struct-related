<?php
return [
    'id' => 'yii2-struct-related',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'runtimePath' => dirname(dirname(__DIR__)) . '/runtime',

    'container' => [
        'definitions' => [
            'dbStorage' => [
                'class' => \kuaukutsu\struct\related\storage\DbStorage::class
            ]
        ],
        'singletons' => [
            'structRelated' => [
                ['class' => \kuaukutsu\struct\related\Related::class],
                [\yii\di\Instance::of('dbStorage')]
            ]
        ],
    ],

    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'sqlite:' . dirname(__DIR__) .'/data/sqllite.db',
        ]
    ]
];