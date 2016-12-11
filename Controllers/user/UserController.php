<?php

use Framework\Database\DB;
use Framework\Database\Table;
use Framework\Controller\Controller;
use Framework\App\Auth;


class UserController extends Controller{

    public function index($id) {
        echo $id;
    }

    public function create() {

        DB::setCurrentConnection('cms');

        $newUser = DB::table(Table::UsersTable())->create(
            [
                'name' => 'second user',
                'password' => '123',
                'email' => 'asd'
            ]
        );
        $newUser->save();

    }

    public function login() {
//        $this->request->email;
//        $this->request->password;

//        $user = Auth::login($this->request->email, $this->request->password);
//        Auth::logout();
//        $user = Auth::user();

        \Framework\Helpers\Helper::dumper($user);
    }

}