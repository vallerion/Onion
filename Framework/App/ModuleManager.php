<?php

namespace Framework\App;

use Framework\Helpers\Helper;
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

        foreach (self::$modules as &$group) {

//            Helper::dumperDie($group['users']['name']);

            self::processModule($group);
        }
    }

    protected static function processModule(&$group) {
        
        foreach ($group as &$module){

            $module['name'] = self::translateField($module['name']);

//            Helper::dumperDie($module);
        }

    }

    protected static function translateField($name) {

        $check = preg_match('|^{locale\.(.*)}$|', $name, $locale);

        if($check)
            return Locale::trans($locale[1]);

        return $check;
    }


}