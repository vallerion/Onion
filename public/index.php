<?php

require __DIR__ . '/../autoload/autoload.php';

use App\App;


$app = App::getInstance();

try {
    $app->run();
}
catch(Throwable $ex){

    // todo: id debug_mode = on -> show exception else only log
    if($app->debug())
        print $ex;
}

print $app;