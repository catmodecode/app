<?php

return [
    'default' => env('DB_DEFAULT', 'postgres'),
    'connections' => [
        'postgres' => [
            'driver'    => env('DB_CONNECTION', 'mysql'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', 'my_app'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'testing' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
    ],
    'migrations' => 'migrations',
];
