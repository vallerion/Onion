<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Http\Routing\Router;

class Route {

    protected $router;

    public function __construct(Router $router){
        $this->router = $router;
    }

    public function __call(string $name, $arguments) {

        list($pattern, $callback) = $arguments;

        if( is_callable($callback) )
            return $this->router->$name($pattern, $callback);


        $controller_data = $this->getControllerMethod($callback);

        $controller_folder = $controller_data['folder'];
        $controller_name = $controller_data['name'];
        $controller_method = $controller_data['method'];

        $path = __DIR__ . '/../../Controllers/' . $controller_folder . '/' . $controller_name . '.php';

        if(file_exists($path))
            require_once $path;
        else
            throw new \Exception('Controller not exist! File: <b>' . $path . '</b>');

        $controller = new $controller_name();

        return $this->router->$name($pattern, [  $controller, $controller_method ]);

//        Helper::dumperDie(is_callable([  $controller, $controller_method ] ));
    }

    protected function getControllerMethod(string $param) {

        list($controller_area, $controller_name, $controller_method) = explode('@', $param);

        return [
            'folder' => strtolower($controller_area),
            'name' => $controller_name,
            'method' => $controller_method
        ];
    }

}