# WP-Custom-Endpoints
A plugin to add custom endpoints to your WordPress REST API

1. Edit route.php to add your custom endpoints.
2. Add your controllers to manage the routes
3. (optional) Edit config.php to setup your API namespace and version.


You'll find all your defined routes in the main JSON at
yourdomain.com//wp-json



### Example
File route.php
```
Route::get('posts', 'PostsController@getAllPosts');
Route::get('posts/{id?}', 'PostsController@getPostById');
Route::get('posts/{category}/{id}', 'PostsController@getPostById');
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
    
    
    public static function getPostById($request)
        {
            if (!empty($request['category'])) {
                // Filter all post by category
            }
    
            if (empty($request['id']))
                return self::getAllPosts([]);
    
            $post = get_post($request['id']);
    
            return self::post_schema($post);
        }

}
```

&nbsp;
&nbsp;

By default you have 3 methods that you can extend in your controller:

```
public function get_permission_callback();
```

If this function returns true, the response will be proccessed
If it returns false,a default error message will be returned
and the request will not proceed with processing

&nbsp;
&nbsp;

```
public function get_validate_callback($param, $request, $key)

```
This function should return true if the value is valid, and false if not.

&nbsp;
&nbsp;

```
public function get_sanitize_callback($param, $request, $key)
```
Used to sanitize the value of the argument before passing it to the main callback.

&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;
&nbsp;

TODO:
Add a 3rd parameter to the routes declaration to define custom callback methods
