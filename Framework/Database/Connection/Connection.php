<?php

namespace Framework\Database\Connection;

use Exception;

class Connection {

    protected $driver;

    protected $validDrivers = [
        'mysql',
        'pgsql',
        'sqlite'
    ];

    protected $host;

    protected $port;

    protected $database;

    protected $user;

    protected $name;

    protected $write;

    protected $read;


    public function __call($name, $arguments) {

        if($name === "password"){

            list(, $password) = explode(':', $this->user);

            return $password;
        }

        if($name === "user"){

            list($user, ) = explode(':', $this->user);

            return $user;
        }

        return $this->$name;
    }


    public function __construct($name, $driver, $host, $port, $database, $user, $write = true, $read = true) {


        if( ! in_array($driver, $this->validDrivers))
            throw new Exception('In ' . __METHOD__ . ' invalid database driver.');

        $this->name = $name;
        $this->driver = $driver;
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;

    }
    
    public function __toString() {
        return $this->toJson();
    }

    public function toJson() {
        return json_encode([
            'driver' => $this->driver,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'user' => $this->user
        ]);
    }

}