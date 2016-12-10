<?php



$route->get('{id}', 'user@UserController@index');

$route->get('/user/create', 'user@UserController@create');

$route->get('cpanel', 'PageController@cpanel');

$route->get('/', function() {
    echo 'hi';
});