<?php

namespace Framework\Traits;

trait Singleton {

    protected static $instance = null;

    protected function __construct(){

    }

    private function __clone(){
        return static::$instance;
    }

    public static function getInstance(...$params){

        if(static::$instance === null)
            static::$instance = new static(...$params);

        return static::$instance;
    }
}