<?php



$route->get('{id}', 'user@UserController@index');

$route->get('/', function() {
    echo 'hi';
});