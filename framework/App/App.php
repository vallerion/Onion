<?php

namespace App;

use Helpers\Helper;
use Support\Singleton;
use Http\Routing\Router;
use Http\Request;
use Http\Response;
use App\Route;

use Database\ORM;

class App extends Singleton {

    private $router;

    private $request;

    private $response;

    private $config = [];


    protected function __construct() {

        $this->router = Router::getInstance();

        $this->request = Request::getInstance();

        $this->response = Response::getInstance();

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

        ORM::configure("$connection_name:host={$config['host']};dbname={$config['database']}");
        ORM::configure('username', $config['username']);
        ORM::configure('password', $config['password']);
    }

    public function __toString() : string {

//        $result = (string)$this->response;

        return '';
    }

    public function debug() {
        return $this->config['debug'];
    }


}