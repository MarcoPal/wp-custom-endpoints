<?php
defined('ABSPATH') or die('No Way!');


require 'WP_Custom_Endpoints.php';

class Router
{

    private $routes = [
        'GET'  => [],
        'POST' => []
    ];

    public static function load($file)
    {
        $router = new static;

        require $file;

        return $router;
    }


    public function define($method, $uri, $controller)
    {
        $this->routes[strtoupper($method)][$uri] = $controller;
    }


    public function dispatch()
    {
        (new WP_Custom_Endpoints())->create($this->routes);
        return $this;
    }


}