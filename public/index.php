<?php

require __DIR__ . '/../vendor/autoload.php';

use Framework\App\App;
use Framework\Log\Log;



$app = App::getInstance();

try {
    $app->run();

    $log = Log::getInstance();

    $log->write('atata');
    $log->error('ALARM!');
    $log->log('test')->error('this is test file');
}
catch(Throwable $ex){

    // todo: id debug_mode = on -> show exception else only log
    if($app->debug())
        print $ex;
}