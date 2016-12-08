<?php

require __DIR__ . '/../autoload/autoload.php';

use App\App;
use Database\DB;
use Database\ORM;



$app = App::getInstance();

try {
    $app->run();

    DB::setCurrentConnection('cms');

    DB::query("
        CREATE TABLE IF NOT EXISTS contact (
            id INTEGER PRIMARY KEY, 
            name TEXT, 
            email TEXT 
        );");

    DB::setCurrentConnection('labki');

    DB::query("
        CREATE TABLE IF NOT EXISTS contact (
            id INTEGER PRIMARY KEY, 
            name TEXT, 
            email TEXT 
        );");
}
catch(Throwable $ex){

    // todo: id debug_mode = on -> show exception else only log
    if($app->debug())
        print $ex;
}

print $app;