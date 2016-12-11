<?php



$route->get('user/{id}', 'user@UserController@index');

$route->get('user/{id}/show/{hash}', function($id, $hash) {
    echo "id: $id<br>";
    echo "hash: $hash<br>";
});

$route->get('/user/create', 'user@UserController@create');

$route->get('cpanel', 'PageController@cpanel');

$route->get('redirect', 'PageController@redirect');

$route->any('/', function() use($response, $request) {

    if($request->isJson())
        $response->write('Hello Json Statham!');
    else if($request->isMedia())
        $response->write('Files: ' . print_r($request->getUploadedFiles(), true));
    else
        $response->write('Hello!');
    
});