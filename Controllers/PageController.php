<?php

use Framework\Controller\Controller;
use Framework\App\ModuleManager;

class PageController extends Controller {




    public function cpanel() {

        $modules = ModuleManager::admin();

//        \Framework\Helpers\Helper::dumper($modules);
    }

}