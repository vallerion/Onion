<?php



$route->get('user/{id}', 'user@UserController@index');

$route->get('/user/create', 'user@UserController@create');

$route->get('cpanel', 'PageController@cpanel');

$route->get('redirect', 'PageController@redirect');

$route->get('/', function() use($response) {
    $response->write('Hello!');
});