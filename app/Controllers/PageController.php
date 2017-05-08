<?php

namespace App\Controllers;

use Framework\Controller\Controller;
use Framework\Database\DB;
use App\Models\User;

class PageController extends Controller {


    public function redirect() {
        $this->response->redirect('/cpanel');
    }

    public function index() {

        $users = User::all();


        return $this->response->view('index', [
            'users' => $users
        ]);
    }

}