EasyCms Engine Doc
=============

## Navigate
- [1. Routing](#1-routing)
- [2. Database](#2-database)
- [2.1 DB class](#21-db-class)
- [2.2 ORM class](#22-orm-class)
- [3. Localization](#3-localization)
- [4. Views](#4-views)
- [4.1 Template engine - Twig](#4-template-engine-twig)


## 1. Routing
Define routes in file route.php.
#### Examples:
```php
$route->get('user/{id}/show/{hash}', function($id, $hash) {
    echo "id: $id<br>";
    echo "hash: $hash<br>";
});

$route->post('user/{id}', function($id) {
    echo "id: $id<br>";
});

$route->put('/', function() use($response) {
    $response->write('THIS IS PUT!');
});

$route->any('/page', function() use($response, $request) {

    if($request->isJson())
        $response->write('Hello Json Statham!');
    else if($request->isMedia())
        $response->write('Files: ' . print_r($request->getUploadedFiles(), true));
    else
        $response->write('Hello!');

});
```

more information: [git][php_routing].


## 2. Database
### 2.1. DB class
DB - main class for working with database.
#### Examples:
```php

use Framework\Database\DB;

 // choose current connect to db
 // all connections specifies in config/db.php
DB::setCurrentConnection('cms');

 // create new user
$newUser = DB::table(Table::UserTable())->create(
    [
        'name' => 'second user',
        'password' => '123',
        'email' => 'asd'
    ]
)->save();

```

or you can use direct query

```php
// query method return PDOStatement
DB::query("
    CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(128) NOT NULL,
      `email` varchar(64) NOT NULL,
      `password` text NOT NULL,
      PRIMARY KEY (`id`)
    );
");

```

### 2.2. ORM class
ORM - is class [idiorm].


## 3. Localization
Just enter locales in config file lang.php
#### Examples:
```php
return [
    'ru',
    'en'
];
```
This means that lang line files will be searched in respective folder (for name).
Write to lang file (ex. test.php):
```php
return [
    // ...
    'hello' => 'Hello, {name}!'
];
```
Next use Locale class
```php
Locale::set('en'); // set current locale

Locale::trans('test.hello', [ 'name' => 'Sanny' ]); // Hello, Sanny!
// test - name of lang file
// hello -  name of lang line
```

## 4. Views
### 4.1. Template engine - Twig
Documentation: [Twig][twig]
Plugin for phpstorm: [link][phpstorm_twig]
### 4.2. Example
in route:
```php
$route->get('template/1', function() use($response) {
    $response->view('rr.test', ['title' => 'Page Title']);
});
```
or in controller:
```php
$route->get('template/2', 'PageController@show');
// ...
public function show() {
    return $this->response->view('hello', ['name' => 'EasyCms Engine']);
}
```

[idiorm]:               https://github.com/j4mie/idiorm
[php_routing]:          https://github.com/valerion1/php_routing
[phpstorm_twig]:        https://blog.jetbrains.com/phpstorm/2013/06/twig-support-in-phpstorm/
[twig]:                 http://twig.sensiolabs.org/