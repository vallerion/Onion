<?php


namespace Database;

use Support\Singleton;
use Database\ORM;

class DB extends Singleton {

    public static function query(string $query) : bool {

        $pdo = ORM::get_db();

        $result = (bool)$pdo->exec($query);

        return $result;
    }


}