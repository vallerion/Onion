<?php

namespace Framework\View;

//use function Framework\View\Twig\Extension\twig_ensure_traversable;
use Framework\View\Twig\Loader\LoaderFilesystem;
use Framework\View\Twig\Environment;
use Framework\Traits\Singleton;

class View {

    use Singleton;

    protected $twig;

    public function __construct() {

        $loader = new LoaderFilesystem(__DIR__ . '/../../View');
        $twig = new Environment($loader);

        $this->twig = $twig;
    }

    public function engine() {
        return $this->twig;
    }


}