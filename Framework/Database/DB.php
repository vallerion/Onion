<?php


namespace Framework\Database;

use Framework\Database\Connection\Connection;
use Framework\Helpers\Helper;
use Framework\Support\Singleton;
use Framework\Database\Connection\ConnectionPool;

use Framework\Database\ORM;

class DB extends Singleton {

    protected static $connectionPool;

    protected static $currentConnection;

    protected function __construct() {
        static::$connectionPool = ConnectionPool::getInstance();
    }


    public static function pushConnection($nameConnection, $driver, $host, $port, $database, $user, $write = true, $read = true) {

        $connection = new Connection($nameConnection, $driver, $host, $port, $database, $user, $write, $read);

        static::$connectionPool[$nameConnection] = $connection;

        static::setCurrentConnection($nameConnection);

//        static::updateConnection();
    }

    protected static function updateConnection() {

        $connection = static::getCurrentConnection();

        ORM::configure( $connection->driver() . ":host={$connection->host()};dbname={$connection->database()}", null, $connection->name());
        ORM::configure('username', $connection->user(), $connection->name());
        ORM::configure('password', $connection->password(), $connection->name());
    }

    public static function setCurrentConnection($name) {
        static::$currentConnection = static::$connectionPool[$name];

        static::updateConnection();
    }

    public static function getCurrentConnection() {

        echo static::$currentConnection;
        return static::$currentConnection;
    }


    public static function query(string $query) {

        $pdo = ORM::get_db(static::$currentConnection->name());

        $result = $pdo->query($query);

        return $result;
    }

    public static function table($tableName){
        return ORM::for_table($tableName, static::$currentConnection->name());
    }


}