<?php
defined('ABSPATH') or die('No Way!');
$router = new Router();

/**
 * you should use one of the following routes, but not both
 *
 * required param: {param}
 * optional param:  {param?}
 *
 * If you use optional params pay attention to your routes definition order
 */
$router->define('GET', '/posts', 'PostsController@getAllPosts');
$router->define('GET', '/posts/{id?}', 'PostsController@getPostById');
$router->define('GET', '/posts/{category}/{id}', 'PostsController@getPostById');