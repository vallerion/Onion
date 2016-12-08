<?php


class Autoloader {

    // карта для соответствия неймспейса пути в файловой системе
    protected $map = [];

    public function add($namespace, $rootDir) {
        if (is_dir($rootDir)) {
            $this->map[$namespace] = $rootDir;
            return true;
        }

        return false;
    }

    public function register() {
//        var_dump($this->map);
        spl_autoload_register([ $this, 'autoload' ]);
    }

    protected function autoload($class) {
        
        $pathParts = explode('\\', $class);

        if (is_array($pathParts)) {
            $namespace = array_shift($pathParts);

            if ( ! empty($this->map[$namespace])) {
                $filePath = $this->map[$namespace] . '/' . implode('/', $pathParts) . '.php';

                require_once $filePath;

                return true;
            }
        }

        return false;
    }

}