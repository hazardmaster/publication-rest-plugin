<?php

/**
 * Plugin Name: Publication REST Plugin
 * Description: This plugin allows you to publish links to you wordpress site using the REST API.
 * Version: 1.0.0
 * Author: Oscar Bett
 * Author URI: https://www.linkedin.com/in/oscar-kipkoech-1988926a/
 * Linkedin: https://www.linkedin.com/in/oscar-kipkoech-1988926a/
 */

function publicationAutomationPost(WP_REST_Request $req){
    // get post data from wp rest api
    $postData = $req->get_params();
    $action = $postData['action'];

    switch ($action) {
        case 'getPageData':
            return getPageData($postData);
        case 'updatePageData':
            return updatePageData($postData);
    }
}

// process all post requests
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', 'publication-automation/', [
        'methods' => 'POST',
        'callback' => 'publicationAutomationPost',
    ]);
});

// Get page data for specified path
function getPageData($postData){
    global $wpdb;
    $path = $postData['path'];
    try {
        $result = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_name LIKE '%$path%'");
        return ['data'=>$result, 'token'=>$postData['token']];
    }catch (Exception $e){
        return ['error' => $e->getMessage(), 'token' => $postData['token']];
    }
}

// Update wp_posts table post_content and post_modified fields
function updatePageData($updateData){
    global $wpdb;
    try{
        $result = $wpdb->update($wpdb->posts, (array)json_decode($updateData['updateData']), ['ID' => $updateData['ID']]);
        return ['success'=>$result, 'token'=>$updateData['token']];
    }catch (Exception $e){
        return ['error' => $e->getMessage(), 'token' => $updateData['token']];
    }
}