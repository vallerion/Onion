<?php

namespace Framework\Model;

use Framework\Database\DB;

class Model {

    protected $table;

    protected $key = 'id';

    public function all() {
        $query = "select * from " . static::table();
        return DB::query($query, static::class);
    }

    public function delete() {
        $query = "delete from {$this->table} where {$this->key} = {$this->{$this->key}}";
        return DB::query($query, static::class);
    }

    public static function table() {
        return (new static)->table;
    }

}