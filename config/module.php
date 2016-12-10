<?php


/**
 * admin - modules that see only admin user(super user)
 *
 * user - modules that see other user
 * 
 */

return [

    'admin' => [

        'users' =>
        [
            'name' => '{locale.module.user_name}',
            'about' => 'Что-то про модуль',
            'access' => 'user', // todo: можно оставлять пустым
            'url' => '/user/' // todo: /user/{self.id}
        ]
    ],


    'user' => [


    ]

];