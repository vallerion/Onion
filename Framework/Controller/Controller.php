<?php

namespace Framework\Controller;

use Framework\Http\Request;
use Framework\Http\Response;

class Controller {

    protected $enabled = true;

    protected $isModule = false;

    protected $response;

    protected $request;

    public function __construct() {

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

    }

    public function enabled() {
        return $this->enabled;
    }

    public function isModule() {
        return $this->isModule;
    }

}