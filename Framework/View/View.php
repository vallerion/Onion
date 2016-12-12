<?php

namespace Framework\View;

//use function Framework\View\Twig\Extension\twig_ensure_traversable;
use Framework\Helpers\Helper;
use Framework\View\Twig\Loader\LoaderFilesystem;
use Framework\View\Twig\Environment;
use Framework\Traits\Singleton;
use InvalidArgumentException;

class View {

    use Singleton;

    protected $twig;

    protected $template;

    protected $values;

    protected function __construct() {

        $loader = new LoaderFilesystem(__DIR__ . '/../../View');
        $twig = new Environment($loader);

        $this->twig = $twig;
    }

    public function engine() {
        return $this->twig;
    }
    
    public function template($template, $values = []){

        if( ! is_string($template) || empty($template) || ! is_array($values))
            throw new InvalidArgumentException('In ' . __METHOD__);

        $template = str_replace('.', '/', $template);

        $template .= '.twig';

        $this->template = $template;
        $this->values = $values;
    }

    public function render() {

        if(is_string($this->template) && ! empty($this->template)) {
            if (!empty($this->values) && is_array($this->values))
                return $this->twig->render($this->template, $this->values);
            else
                return $this->twig->render($this->template);
        }

        return '';
    }

    public function exist() {
        return file_exists(__DIR__ . '/../../View' . $this->template);
    }


}