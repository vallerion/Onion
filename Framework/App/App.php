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

    private $configApp = [];

    private $configDb = [];


    protected function __construct() {

        $this->router = Router::getInstance();

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

        $this->db = DB::getInstance();



        $this->setConfig();

        $this->setDatabaseConfig();

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
        $this->configApp =  include __DIR__ . '/../config/config.php';
        $this->configDb =  include __DIR__ . '/../../config/db.php';
    }

    protected function setDatabaseConfig() {

        $connections = $this->configDb;

        foreach ($connections as $key => $value){
            DB::pushConnection($key, $value['driver'], $value['host'], $value['port'], $value['database'], $value['username'] . ':' . $value['password'], strpos($value['mode'], 'write'), strpos($value['mode'], 'read'));
        }

//        $config = $this->config['connections'][$connection_name];
//
//
//        DB::pushConnection('cms', 'mysql', $config['host'], $config['port'], $config['database'], $config['username'] . ':' . $config['password']);
//        DB::pushConnection('testDB', 'mysql', $config['host'], $config['port'], 'testDB', $config['username'] . ':' . $config['password']);


        
    }

    public function __toString() : string {

//        $result = (string)$this->response;

        return '';
    }

    public function debug() {
        return $this->configApp['debug'];
    }


}