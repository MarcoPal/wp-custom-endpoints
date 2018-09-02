<?php
defined('ABSPATH') or die('No Way!');


class PostsController extends WP_Custom_Endpoints
{


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Example method to retrieve all posts
     *
     * @param $request WP_REST_Request|array
     * @return array
     */


    public static function getAllPosts($request)
    {
        $posts = get_posts(['posts_per_page' => 5]);


        return array_map(function ($post) {
            return self::post_schema($post);
        }, $posts);
    }


    /**
     * Example method to retrieve a post by his own ID
     *
     * @param $request WP_REST_Request
     * @return array
     */
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


    /**
     * Example method to retrieve a post by his own slug
     *
     * @param $request WP_REST_Request
     * @return array|string
     */
    public static function getPostBySlug($request)
    {

        $args  = array(
            'name'        => $request['slug'],
            'post_type'   => 'post',
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $posts = get_posts($args);

        if ($posts)
            return self::post_schema($posts[0]);

        return 'post not found';
    }


    /**
     * Suggested method to filter your response
     *
     * @param $post
     * @return array
     */
    public static function post_schema($post)
    {
        $schema            = [];
        $schema['ID']      = $post->ID;
        $schema['content'] = $post->post_content;
        $schema['slug']    = $post->post_name;

        return $schema;
    }


    /**
     * This callback should return a boolean or a WP_Error instance.
     * If this function returns true, the response will be proccessed.
     * If it returns false, a default error message will be returned
     * and the request will not proceed with processing
     *
     * If you don't need to check permission, you can delete this method from here
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
     * If you don't need to validate your param, you can delete this method from here
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
     * If you don't need to sanitize your param, you can delete this method from here
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