<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Traits\Singleton;
use Framework\Http\Routing\Router;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Database\DB;
use Framework\Database\Table;

class App {

    use Singleton;

    private $router;

    private $request;

    private $response;

    private $db;

    private $table;

    private $locale;

    private $auth;

    private $configApp = [];

    private $configDb = [];


    protected function __construct() {


        $this->setConfig();

        $this->auth = Auth::getInstance($this->config('session_name'));

        $this->router = Router::getInstance();

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

        $this->db = DB::getInstance();

        $this->table = Table::getInstance();

        $this->locale = Locale::getInstance();

        $this->makeDbConnections();

    }

    public function run() {

        $this->routing();


        $this->response->respond();

        print $this;
    }

    protected function routing() {

        $route = new Route($this->router);
        $response = $this->response;
        $request = $this->request;

        require __DIR__ . '/../../route.php';

        $this->router->run();
    }

    protected function setConfig() {
        $this->configApp =  include __DIR__ . '/../../config/env.php';
        $this->configDb =  include __DIR__ . '/../../config/db.php';
    }

    protected function makeDbConnections() {

        $connections = $this->configDb;

        foreach ($connections as $key => $value){
            DB::pushConnection($key, $value['driver'], $value['host'], $value['port'], $value['database'], $value['username'] . ':' . $value['password'], stristr($value['mode'], 'write'), stristr($value['mode'], 'read'));
        }
        
    }

    public function __toString() {

//        $result = (string)$this->response;

        return (string)$this->response;
    }

    public function debug() {
        return (bool)$this->configApp['debug'];
    }

    public function config($parameter) {
        return isset($this->configApp[$parameter]) ? $this->configApp[$parameter] : false;
    }


}