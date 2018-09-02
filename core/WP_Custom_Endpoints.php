<?php
defined('ABSPATH') or die('No Way!');

require 'Param.php';


class WP_Custom_Endpoints
{


    private $config;
    private $namespace;
    public $param, $request, $key;


    public function __construct()
    {
        $this->config    = $this->set_config();
        $this->namespace = $this->set_namespace();

    }


    /**
     * It all starts here
     *
     * @param $routes
     */
    public function create($routes)
    {

        foreach ($routes as $requestType => $route) {
            foreach ($route as $uri => $controller) {
                $this->register_endpoint($requestType, $uri, ...explode('@', $controller));
            }
        }

    }


    /**
     * Setup the configuration file
     *
     * @return mixed
     */
    private function set_config()
    {
        global $wpendpoints;
        return $wpendpoints['config'];
    }


    /**
     * Set the namespace for API calls
     *
     * @return string
     */
    private function set_namespace()
    {

        $namespace = !empty($this->config['namespace']) ? $this->config['namespace'] : 'wp-custom-endpoints';
        $version   = !empty($this->config['version']) ? $this->config['version'] : 'v1';

        return '/' . $namespace . '/' . $version;
    }


    /**
     * Used in the create method to register new routes
     *
     * @param $requestType : should be GET or POST
     * @param $uri : the uri from the route declaration
     * @param $controller :  controller for current uri
     * @param $method : method from the current controller
     */
    private function register_endpoint($requestType, $uri, $controller, $method)
    {

        $parameters = $this->format_uri_parameters($uri);

        $uri = $this->prepare_uri($parameters);

        $args = $this->create_route_array($requestType, $controller, $method, $uri, $parameters);

        register_rest_route($this->namespace, $uri, $args);
    }

    /**
     * Returns the formatted uri
     *
     * @param $parameters
     * @return string
     */

    private function prepare_uri($parameters)
    {
        $uri = array_map(function ($param) {
            return $param['uri'];
        }, $parameters);

        return implode('', $uri);
    }


    /**
     * Creates the array that will be used during the route registration
     *
     * @param $requestType
     * @param $controller
     * @param $method
     * @param $uri
     * @return array
     */
    private function create_route_array($requestType, $controller, $method, $uri, $parameters)
    {

        $response = [];

        $response['methods'] = $requestType;

        if (method_exists($controller, $method))
            $response['callback'] = [$controller, $method];

        $response['permission_callback'] = [$controller, 'get_permission_callback'];

        $parameters = array_filter($parameters, function ($param) {
            return isset($param['param']);
        });

        foreach ($parameters as $param) {
            $current_param = $param['param'];


            $response['args'][$current_param]['required'] = $param['required'];

            $response['args'][$current_param]['validate_callback'] = function ($param, $request, $key) use ($controller) {
                return (new $controller())->get_validate_callback($param, $request, $key);
            };

            $response['args'][$current_param]['sanitize_callback'] = function ($param, $request, $key) use ($controller) {
                return (new $controller())->get_sanitize_callback($param, $request, $key);
            };

        }

        return $response;
    }


    /**
     * Format the uri parameters and setup the required attribute
     *
     * @param $uri
     * @return array|mixed
     */
    private function format_uri_parameters($uri)
    {
        $parameters = explode('/', ltrim($uri, '/'));

        return array_map(function ($param) {

            if (!Param::is_param($param)) {

                return [
                    'uri' => '/' . $param
                ];

            } else if (Param::ends_with($param, '?}')) {
                $param = rtrim(trim($param, '{}'), '?');

                return [
                    'uri'      => Param::format_optional($param),
                    'param'    => $param,
                    'required' => false];
            }

            $param = trim($param, '{}');

            return [
                'uri'      => Param::format_required($param),
                'param'    => $param,
                'required' => true
            ];

        }, $parameters);

    }


    /**
     * This callback should return a boolean or a WP_Error instance.
     * If this function returns true, the response will be proccessed.
     * If it returns false, a default error message will be returned
     * and the request will not proceed with processing
     *
     * If you edit this method here, it will be applied globally to all your routes,
     * if you need it only in a certain controller, you should redeclare in it
     *
     * @return bool
     */
    public function get_permission_callback()
    {
        return true;
    }


    /**
     * This function should return true if the value is valid, and false if not.
     *
     * If you edit this method here, it will be applied globally to all your routes,
     * if you need it only in a certain controller, you should redeclare in it
     *
     * @param $param
     * @param $request
     * @param $key
     * @return bool
     */
    public function get_validate_callback($param, $request, $key)
    {

        return true;
    }


    /**
     * Used to sanitize the value of the argument before passing it to the main callback.
     *
     * If you edit this method here, it will be applied globally to all your routes,
     * if you need it only in a certain controller, you should redeclare in it
     *
     * @param $param
     * @param $request
     * @param $key
     * @return mixed
     */
    public function get_sanitize_callback($param, $request, $key)
    {
        return $param;
    }


}