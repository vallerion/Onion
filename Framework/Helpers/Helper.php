<?php

namespace Framework\Helpers;

class Helper{

    public static function dumperDie($arg){

        echo '<pre>';
        echo htmlspecialchars(var_dump($arg));
        echo '</pre><br>';

        exit;
    }

    public static function dumper($arg){

        echo '<pre>';
        echo htmlspecialchars(var_dump($arg));
        echo '</pre><br>';

    }

    public static function show($arg) {

        echo '<pre>';
        echo htmlspecialchars($arg);
        echo '</pre><br>';
    }

    public static function showDie($arg) {

        echo '<pre>';
        echo htmlspecialchars($arg);
        echo '</pre><br>';

        exit;
    }

}