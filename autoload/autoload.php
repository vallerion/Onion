<?php


require __DIR__ . '/Autoloader.php';

$loader = new Autoloader();

      // register the autoloader
      $loader->register();

      // register the base directories for the namespace prefix
      $loader->addNamespace('App', __DIR__ . '/../Framework/App');
//      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');

//$autoloader = new Autoloader();
//
//$autoloader->add('Helpers', __DIR__ . '/../Framework/Helpers');
//$autoloader->add('Http', __DIR__ . '/../Framework/Http');
//$autoloader->add('Support', __DIR__ . '/../Framework/Support');
//$autoloader->add('Traits', __DIR__ . '/../Framework/Traits');
//$autoloader->add('App', __DIR__ . '/../Framework/App');
//$autoloader->add('Database', __DIR__ . '/../Framework/Database');
//$autoloader->add('Psr', __DIR__ . '/../Framework/Psr');
//
//$autoloader->register();