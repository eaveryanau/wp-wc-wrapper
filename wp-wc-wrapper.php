<?php
/*
Plugin Name: WP WC wrapper
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

        // Done.
        case BASE_HUB_API_URI . 'products/index':
            $response = ProductManager::getProducts();
            break;

        case (preg_match(',' . BASE_HUB_API_URI . 'products/view/([0-9]+$),', $request['uri'], $m) ? true : false) :
            $response = ProductManager::getProduct($m[1]);
            break;

        case (preg_match(',' . BASE_HUB_API_URI . 'products/edit/([0-9]+$),', $request['uri'], $m) ? true : false) :
            if($request['method'] == 'POST'){
                $response = ProductManager::updateProduct($m[1], $request['data']);
            }
            else{
                $response = ProductManager::getProduct($m[1]);
            }
            break;

        default:
            die('default');
            $response = false;
            break;
    }

    return $response;
}
