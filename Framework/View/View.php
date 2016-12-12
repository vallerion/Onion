<?php

namespace Framework\View;

use Framework\View\Twig\Loader\LoaderFilesystem;
use Framework\View\Twig\Environment;

class View {

    protected $twig;

    public function __construct() {

        $loader = new LoaderFilesystem(__DIR__ . '/../View');
        $twig = new Environment($loader);

    }


}