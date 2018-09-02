<?php
defined('ABSPATH') or die('No Way!');


require 'WP_Custom_Endpoints.php';

class Route
{

    private static $routes = [
        'GET'  => [],
        'POST' => []
    ];

    public static function load($file)
    {
        $router = new static;

        require $file;

        return $router;
    }


    public static function get($uri, $controller)
    {
        self::$routes['GET'][$uri] = $controller;
    }


    public static function post($uri, $controller)
    {
        self::$routes['POST'][$uri] = $controller;
    }

    public function dispatch()
    {
        (new WP_Custom_Endpoints())->create(self::$routes);
        return $this;
    }


}