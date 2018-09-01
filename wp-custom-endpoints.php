<?php
/*
Plugin Name:  WP Custom Endpoints
Plugin URI:   https://github.com/marcopal
Description:  Adds custom endpoints to your WP REST API - Edit the file route.php to add your custom endpoints and the file config.php to setup your namespace.
Version:      0
Author:       Marco Pal
Author URI:   https://github.com/marcopal
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  _
Domain Path:  _
*/


defined('ABSPATH') or die('No Way!');


require __DIR__ . '/core/Router.php';


$wpendpoints['config'] = require __DIR__ . '/config.php';


spl_autoload_register(function ($class) {
    $classFile = __DIR__ . "/controllers/{$class}.php";
    if (file_exists($classFile)) {
        require_once $classFile;
        return true;
    }
    return false;
});


function wp_custom_endpoints()
{
    return Router::load(__DIR__ . '/routes.php')->dispatch();
}

add_action('rest_api_init', 'wp_custom_endpoints');