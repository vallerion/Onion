<?php

use Framework\Controller\Controller;
use Framework\App\ModuleManager;

class PageController extends Controller {


    public function redirect() {

        $this->response->redirect('/cpanel');
    }

    public function cpanel() {

        $modules = ModuleManager::admin();
    }
    
    public function show() {
        return $this->response->view('hello', ['name' => 'EasyCms Engine']);
    }

}