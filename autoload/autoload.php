<?php


require __DIR__ . '/Autoloader.php';

$autoloader = new Autoloader();

$autoloader->add('Helpers', __DIR__ . '/../framework/Helpers');
$autoloader->add('Http', __DIR__ . '/../framework/Http');
$autoloader->add('Support', __DIR__ . '/../framework/Support');
$autoloader->add('Traits', __DIR__ . '/../framework/Traits');
$autoloader->add('App', __DIR__ . '/../framework/App');
$autoloader->add('Database', __DIR__ . '/../framework/Database');
//$autoloader->add('Controllers', __DIR__ . '/../Controllers');

$autoloader->register();

//spl_autoload_register(function ($class) {
//
//    echo $class;
//
//    $class = str_replace('\\', '/', $class);
//
//    include __DIR__ . "/$class.php";
//});