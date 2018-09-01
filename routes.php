<?php
defined('ABSPATH') or die('No Way!');
$router = new Router();


$router->define('GET', '/posts', 'PostsController@getAllPosts');

/**
 * you should use one of the following routes, but not both
 *
 * required param: {param}
 * optional param:  {param?}
 *
 */
$router->define('GET', '/posts/{id}', 'PostsController@getPostById');
//$router->define('GET', '/posts/{slug?}', 'PostsController@getPostBySlug');