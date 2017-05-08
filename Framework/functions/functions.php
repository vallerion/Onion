<?php

if ( ! function_exists('dumper')) {
    function dumper()
    {
        $args = func_get_args();

        echo '<pre>';
        echo htmlspecialchars(print_r($args, true));
        echo '</pre><hr>';
    }
}

if ( ! function_exists('ddumper')) {
    function ddumper()
    {
        $args = func_get_args();

        echo '<pre>';
        echo htmlspecialchars(print_r($args, true));
        echo '</pre><hr>';

        die(1);
    }
}

if ( ! function_exists('show')) {
    function show()
    {
        $args = func_get_args();

        echo '<pre>';
        echo print_r($args, true);
        echo '</pre><hr>';
    }
}