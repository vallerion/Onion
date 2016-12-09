<?php

return [

    'sqlite' => [
        'driver' => 'sqlite',
        'database' => '',
        'prefix' => '',
    ],

    'mysql' => [
        'mode' => 'write|read',
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'EasyCMS',
        'username' => 'admin',
        'password' => 'password',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ],

    'pgsql' => [
        'driver' => 'pgsql',
        'host' => '',
        'port' => '5432',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
    ],
    
    
];
