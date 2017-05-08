<?php

namespace Framework\View;

use Framework\Traits\Singleton;

class View {

    use Singleton;

    protected $templates = [];

    public function render($template = null) {
        if( ! is_null($template))
            return $this->renderTemplate($template);
        else {
            $template = array_values($this->templates);
            return isset($template[0]) ? $template->render() : '';
        }
    }

    protected function renderTemplate($template) {

        if( ! isset($this->templates[$template]))
            $this->putTemplate($template);

        return $this->templates[$template]->render();
    }

    public function template($template, array $values) {
        $this->putTemplate($template);

        foreach ($values as $key => $value)
            $this->templates[$template]->set($key, $value);
    }

    public function putTemplate($template) {
        $this->templates[$template] = new Template($template);
    }
    
    


}