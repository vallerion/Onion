<?php

namespace Framework\App;

use Framework\Traits\Singleton;

class ModuleManager {

    use Singleton;

    protected static $modules;


    protected function __construct() {

    }

    public static function admin() {

        if(empty(self::$modules))
            self::loadModules();

        return self::$modules['admin'];
    }

    public static function user() {

        if(empty(self::$modules))
            self::loadModules();

        return self::$modules['user'];
    }

    protected static function loadModules() {
        self::$modules =  include __DIR__ . '/../../config/module.php';

        foreach (self::$modules as $group)
            self::processModule($group);
    }

    protected static function processModule($group) {
        
        foreach ($group as $module){
            
        }

    }


}