# WP-Custom-Endpoints
A plugin to add custom endpoints to your WordPress REST API

Edit the file config.php to setup your API namespace.

Edit the file route.php to add your custom endpoints.

Add your controllers to manage the routes

### Example
File route.php
```
$router->define('GET', '/posts', 'PostsController@getAllPosts');
$router->define('GET', '/posts/{id}', 'PostsController@getPostById');
```

Your controller:
```
class PostsController extends WP_Custom_Endpoints
{

    public static function getAllPosts()
    {
        $posts = get_posts(['posts_per_page' => 5]);
        return array_map(function ($post) {
            return self::post_schema($post);
        }, $posts);
    }

}
```
