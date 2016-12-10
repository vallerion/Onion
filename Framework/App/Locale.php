<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Traits\Singleton;


class Locale {

    use Singleton;

    protected static $currentLocale;

    protected static $validLocale = [];


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


}