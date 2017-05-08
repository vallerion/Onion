<?php

namespace App\Controllers;

use App\Models\Author;
use App\Models\Object;
use Framework\Controller\Controller;
use Framework\Database\DB;
use App\Models\User;

class PageController extends Controller {

    protected $object;

    protected $user;

    protected $author;

    public function __construct()
    {
        parent::__construct();


        $this->object = new Object();
        $this->author = new Author();
        $this->user = new User();
    }


    public function redirect() {
        $this->response->redirect('/cpanel');
    }

    public function index() {

        $topObjects = $this->object->top();
        $topAuthors = $this->author->top();
        $topUsers = $this->user->top();
//        ddumper($topAuthors);


        return $this->response->view('index', [
            'objects' => $topObjects,
            'authors' => $topAuthors,
            'users' => $topUsers,
        ]);
    }

}