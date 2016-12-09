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


    public static function pushConnection($nameConnection, $driver, $host, $port, $database, $user) {

        $connection = new Connection($driver, $host, $port, $database, $user);

        static::$connectionPool[$nameConnection] = $connection;

        static::setCurrentConnection($nameConnection);

        static::updateConnection();
    }

    protected static function updateConnection() {

        $connection = static::getCurrentConnection();

//        Helper::show($connection->driver());

        ORM::configure( $connection->driver() . ":host={$connection->host()};dbname={$connection->database()}");
        ORM::configure('username', $connection->user());
        ORM::configure('password', $connection->password());
    }

    public static function setCurrentConnection($name) {
        static::$currentConnection = static::$connectionPool[$name];

        static::updateConnection();
    }

    public static function getCurrentConnection() {
        return static::$currentConnection;
    }


    public static function query(string $query) : bool {

        $pdo = ORM::get_db();

        $result = (bool)$pdo->exec($query);

        return $result;
    }


}