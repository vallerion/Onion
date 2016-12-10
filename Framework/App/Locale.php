<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Traits\Singleton;


class Locale {

    use Singleton;

    protected static $currentLocale;

    protected static $validLocale = [];

    protected static $translateContainer = []; // todo: create translate container class


    protected function __construct() {

        if(empty(self::$validLocale))
            self::loadConfig();

        self::$currentLocale = self::$validLocale[0];
    }


    public static function set($locale) {

        $locale = strtolower($locale);

        $check = in_array($locale, self::$validLocale);

        if($check)
            self::$currentLocale = $locale;

        return $check;
    }

    public static function get() {
        return self::$currentLocale;
    }

    protected static function loadConfig() {
        self::$validLocale =  include __DIR__ . '/../../config/lang.php';
    }
    
    public static function trans($name, $params = []) {

        $filename = explode('.', $name)[0];
        $keyword = explode('.', $name)[1];

        $check = isset(self::$translateContainer[self::$currentLocale][$filename]);

        if( ! $check){

            $file = __DIR__ . '/../../lang/' . self::$currentLocale . '/' . $filename . '.php';

            self::$translateContainer[self::$currentLocale][$filename] = self::getTranslateFIle($file);
            
        }

        $check = isset(self::$translateContainer[self::$currentLocale][$filename][$keyword]);

        if($check){

            $translate = self::$translateContainer[self::$currentLocale][$filename][$keyword];

            if( ! empty($params))
                $translate = self::replaceParams($translate, $params);

            return $translate;
        }


        return false;

    }

    protected static function replaceParams($name, $params) {

        $patterns = array_keys($params);
        $values = array_values($params);

        foreach ($patterns as &$pattern)
            $pattern = '|{' . $pattern . '}|';

        $name = preg_replace($patterns, $values, $name);

        return $name;
    }
    
    protected static function getTranslateFIle($file) {
        return include $file;
    }


}