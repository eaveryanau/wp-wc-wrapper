<?php
/*
Plugin Name: Uha
Description: Universal hub data aggregator
Version: 1.0
Author: Averyanau Yauheni
Author URI:
Plugin URI:
*/

// Define constant.
define('BASE_HUB_API_URI', '/rest/v1/');


// Include methods for any entity.
require_once('app/bootstrap.php');

// Check any requests.
add_action('parse_request', 'urlHandler');
function urlHandler()
{
    $request = [
        'uri' => $_SERVER['REQUEST_URI'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'data' => $_REQUEST
    ];
    if (strstr($request['uri'], BASE_HUB_API_URI)) {
        $response = getResponseForHub($request);
        wp_send_json($response);
    }
}

// Processing requests.
function getResponseForHub($request)
{
    switch ($request['uri']) {
        case BASE_HUB_API_URI . 'products/index':
            $response = ProductManager::getProducts();
            break;

        default:
            $response = false;
            break;
    }

    return $response;
}
