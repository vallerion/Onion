<?php


//$route->get('/', function(){
//    echo "Hello!";
//});

$route->get('{id}', 'UserController@index');

$route->get('/', function() {
    echo 'hi';
});