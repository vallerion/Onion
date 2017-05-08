<?php


namespace Framework\Database;

use Framework\Database\Connection\Connection;
use Framework\Helpers\Helper;
use Framework\Traits\Singleton;
use Framework\Database\Connection\ConnectionPool;

use Framework\Database\ORM;

class DB {

    use Singleton;

    protected static $connectionPool;

    protected static $currentConnection;

    protected function __construct() {
        static::$connectionPool = ConnectionPool::getInstance();
    }


    public static function pushConnection($nameConnection, $driver, $host, $port, $database, $user, $write = true, $read = true) {

        $connection = new Connection(
            $nameConnection,
            $driver,
            $host,
            $port,
            $database,
            $user,
            $write,
            $read
        );

        static::$connectionPool[$nameConnection] = $connection;

        static::setCurrentConnection($nameConnection);
    }

    public static function setCurrentConnection($name) {
        static::$currentConnection = static::$connectionPool[$name];
    }

    public static function getCurrentConnection() {
        return static::$currentConnection;
    }


}