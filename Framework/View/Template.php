<?php

namespace Framework\View;


class Template {

    protected $file;

    protected $values = [];

    public function __construct($file = null)
    {
        if( ! is_null($file))
            $this->setFile($file);
    }

    public function setFile($file) {

        $path = str_replace('.', '/', $file);
        $path = __DIR__ . '/../../app/Views/' . $path . '.php';

        if(file_exists($path))
            $this->file = $path;
        else
            throw new \Exception("{$file}.php was not found");

    }

    public function set($key, $value) {
        $this->values[$key] = $value;

        return $this;
    }

    public function render() {
        return template($this->file, $this->values);
    }


}