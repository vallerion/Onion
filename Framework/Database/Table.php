<?php


namespace Framework\Database;

use Framework\Traits\Singleton;

class Table {

    use Singleton;

    protected static $tables = [];

    public function __construct() {
        $this->loadTables();
    }

    protected function loadTables() {
        self::$tables =  include __DIR__ . '/../../config/tables.php';
    }

    public static function __callStatic($name, $arguments) {
        return self::$tables[$name];
    }

}