<?php

use Framework\Database\DB;
use Framework\Database\Table;


class UserController {

    public function index($id) {
        echo $id;
    }

    public function create() {

        DB::setCurrentConnection('cms');

        $newUser = DB::table(Table::UserTable())->create(
            [
                'name' => 'second user',
                'password' => '123',
                'email' => 'asd'
            ]
        )->save();

    }

}