<?php
defined('ABSPATH') or die('No Way!');


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
        return $wpendpoints;
    }


    /**
     * Set the namespace for API calls
     *
     * @return string
     */
    private function set_namespace()
    {

        $namespace = !empty($this->config['namespace']) ? $this->config['namespace'] : 'wp-endpoints';
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

        $uri  = $this->prepre_uri($uri);
        $args = $this->create_route_array($requestType, $controller, $method, $uri);

        register_rest_route($this->namespace, $uri['uri'], $args);
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
    private function create_route_array($requestType, $controller, $method, $uri)
    {

        $response = [];

        $response['methods'] = $requestType;

        if (method_exists($controller, $method))
            $response['callback'] = [$controller, $method];

        $response['permission_callback'] = [$controller, 'get_permission_callback'];

        if (!empty($uri['param'])) {

            $param = $uri['param'];

            $response['args'][$param]['required'] = $uri['required'];

            $response['args'][$param]['validate_callback'] = function ($param, $request, $key) use ($controller) {
                return (new $controller())->get_validate_callback($param, $request, $key);
            };

            $response['args'][$param]['sanitize_callback'] = function ($param, $request, $key) use ($controller) {
                return (new $controller())->get_sanitize_callback($param, $request, $key);
            };
        }

        return $response;
    }


    /**
     * Check the uri parameters and setup the required attribute
     *
     * @param $uri
     * @return array|mixed
     */
    private function prepre_uri($uri)
    {
        $uri_parts = explode('/', $uri);
        $last_part = end($uri_parts);
        $uri_arr   = [];

        if ($this->uri_has_param($last_part)) {
            list($uri_arr, $uri) = $this->generate_new_uri($last_part, $uri_arr, $uri_parts);
        }

        $uri_arr['uri'] = $uri;

        return $uri_arr;
    }


    /**
     * Check if uri has a parameter
     *
     * @param $last_part
     * @return int
     */
    private function uri_has_param($last_part)
    {
        return preg_match('/{(.*?)}/', $last_part);
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


    /**
     * @param $last_part
     * @param $uri_arr
     * @param $uri_parts
     * @return array
     */
    private function generate_new_uri($last_part, $uri_arr, $uri_parts)
    {
        $last_part = trim($last_part, '{}');

        if (substr($last_part, -1) === '?') {

            $last_part           = rtrim($last_part, '?');
            $parm                = '(?:/(?P<' . $last_part . '>\S+))?';
            $uri_arr['required'] = false;

        } else {

            $parm                = '/(?P<' . $last_part . '>[\S]+)';
            $uri_arr['required'] = true;
        }

        $uri_arr['param'] = $last_part;

        array_pop($uri_parts);
        $uri = implode('/', $uri_parts) . $parm;

        return array($uri_arr, $uri);
    }


}