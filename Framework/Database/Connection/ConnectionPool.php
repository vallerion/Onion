<?php

namespace Database\Connection;

use ArrayAccess;
use Support\Singleton;


class ConnectionPool extends Singleton implements ArrayAccess {


    protected $container = [];


    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function offsetSet($offset, $value) {

        if (is_null($offset)) {
            $this->container[] = $value;
        }
        else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }


}