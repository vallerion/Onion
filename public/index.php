<?php

require __DIR__ . '/../vendor/autoload.php';

use Framework\App\App;


$app = App::getInstance();

try {

    $app->run();
}
catch(Throwable $ex){

    // todo: id debug_mode = on -> show exception else only log
    if($app->debug())
        print $ex;

    Log::getInstance()->error($ex->getMessage());
}