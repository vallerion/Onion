<?php


/**
 * list of alias name => real table name in db
 *
 * Usage:
 *
 * just call as static method Table class
 *
 * ex: Table::UserTable(); // users
 *
 */

return [

    'user' => [

        'users' =>
        [
            'name' => 'Пользователи', // todo: 'name' => '{locale.module.user_name}'
            'about' => 'Что-то про модуль',
            'access' => 'user', // todo: можно оставлять пустым
            'url' => '/user/' // todo: /user/{self.id}
        ]
    ],


    'admin' => [


    ]

];