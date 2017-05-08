<?php

use Framework\Controller\Controller;
use Framework\Database\DB;

class PageController extends Controller {


    public function redirect() {
        $this->response->redirect('/cpanel');
    }

    public function index() {

        $users = DB::query('
            select * from users
        ');
        

        return $this->response->view('index', [
            'users' => $users
        ]);
    }

}