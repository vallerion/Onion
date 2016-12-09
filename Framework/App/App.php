<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Support\Singleton;
use Framework\Http\Routing\Router;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Database\DB;

class App extends Singleton {

    private $router;

    private $request;

    private $response;

    private $db;

    private $config = [];


    protected function __construct() {

        $this->router = Router::getInstance();

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

        $this->db = DB::getInstance();



        $this->setConfig();

        $this->setDatabaseConfig();

//        Helper::dumperDie($this->config);

    }

    public function run() {

        $this->routing();

    }

    protected function routing() {

        $route = new Route($this->router);

        require __DIR__ . '/../../route.php';

        $this->router->run();
    }

    protected function setConfig() {
        $this->config =  include __DIR__ . '/../config/config.php';
    }

    protected function setDatabaseConfig() {

        $connection_name = $this->config['connection'];

        $config = $this->config['connections'][$connection_name];


        DB::pushConnection('cms', 'mysql', $config['host'], $config['port'], $config['database'], $config['username'] . ':' . $config['password']);
        DB::pushConnection('labki', 'mysql', $config['host'], $config['port'], 'labki', $config['username'] . ':' . $config['password']);


        
    }

    public function __toString() : string {

//        $result = (string)$this->response;

        return '';
    }

    public function debug() {
        return $this->config['debug'];
    }


}