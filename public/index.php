<?php

require __DIR__ . '/../vendor/autoload.php';

use Framework\App\App;


$app = App::getInstance();

try {

    $app->run();
}
catch(Throwable $ex){
    
    if($app->debug())
        print $ex;

    $app->log()->error($ex->getMessage());
}