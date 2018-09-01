# WP-Custom-Endpoints
A plugin to add custom endpoints to your WordPress REST API


Edit the file route.php to add your custom endpoints.

Edit the file config.php to setup your API namespace.

### Example
```
$router->define('GET', '/posts', 'PostsController@getAllPosts');
$router->define('GET', '/posts/{id}', 'PostsController@getPostById');
```