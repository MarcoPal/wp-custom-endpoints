<?php
defined('ABSPATH') or die('No Way!');

/**
 * you should use one of the following routes, but not both
 *
 * required param: {param}
 * optional param:  {param?}
 *
 * If you use optional params pay attention to your routes definition order
 */
Route::get('posts', 'PostsController@getAllPosts');
Route::get('posts/{id?}', 'PostsController@getPostById');
Route::get('posts/{category}/{id}', 'PostsController@getPostById');