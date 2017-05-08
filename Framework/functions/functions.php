<?php

if ( ! function_exists('dumper')) {
    function dumper()
    {
        $args = func_get_args();

        echo '<pre>';
        foreach ($args as $arg)
            echo htmlspecialchars(print_r($arg, true)) . chr(10);
        echo '</pre><hr>';
    }
}

if ( ! function_exists('ddumper')) {
    function ddumper()
    {
        $args = func_get_args();

        echo '<pre>';
        foreach ($args as $arg)
            echo htmlspecialchars(print_r($arg, true)) . chr(10);
        echo '</pre><hr>';

        die(1);
    }
}

if ( ! function_exists('show')) {
    function show()
    {
        $args = func_get_args();

        echo '<pre>';
        foreach ($args as $arg)
            echo print_r($arg, true) . chr(10);
        echo '</pre><hr>';
    }
}

if ( ! function_exists('template')) {
    function template($file, array $args)
    {
        extract($args);

        ob_start();
        require($file);
        return ob_get_clean();
    }
}

if ( ! function_exists('url')) {
    function url($path)
    {
        if (filter_var($path, FILTER_VALIDATE_URL) === FALSE) {
            $request = \Framework\Http\Request::getInstance();
            $host = $request->host();
            $scheme = $request->scheme();

            return "$scheme://$host/$path";
        }

        return $path;
    }
}