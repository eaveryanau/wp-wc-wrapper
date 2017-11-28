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
define( 'BASE_HUB_API_URI', '/rest/v1/' );


// Include methods for any entity.
require_once( 'app/bootstrap.php' );

// Check any requests.
add_action( 'parse_request', 'urlHandler' );
function urlHandler() {

	$uri_help = strstr( $_SERVER['REQUEST_URI'], '?', true );
	$request  = [
		'uri'          => ( $uri_help ) ? $uri_help : $_SERVER['REQUEST_URI'],
		'method'       => $_SERVER['REQUEST_METHOD'],
		'data'         => $_REQUEST,
		'access-token' => ( isset($_SERVER['HTTP_ACCESS_TOKEN']) ) ? $_SERVER['HTTP_ACCESS_TOKEN'] : null
	];
	if ( strstr( $request['uri'], BASE_HUB_API_URI ) ) {
		if (checkSecureKey( $request['access-token'] ) ) {
			$response = getResponseForHub( $request );
			wp_send_json( $response );
		} else {
			/*wp_send_json( [ 'data' => null, 'error' => 'Request not authenticated.' ] );*/
			$response = getResponseForHub( $request );
			wp_send_json( $response );
			
		}
	}
}

// Processing requests.
function getResponseForHub( $request ) {
	switch ( $request['uri'] ) {

		// Done.
		case BASE_HUB_API_URI . 'products/index':
			$response = ProductManager::getProducts();
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'products/view/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			$response = ProductManager::getProduct( $m[1] );
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'products/edit/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = ProductManager::updateProduct( $m[1], $request['data'] );
			} else {
				$response = ProductManager::getProduct( $m[1] );
			}
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'products/delete/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = ProductManager::deleteProduct( $m[1] );
			} else {
				$response = [ 'error' => 'wrong route' ];
			}
			break;

		case BASE_HUB_API_URI . 'orders/index':
			$response = OrderManager::getOrders();
			break;
		case BASE_HUB_API_URI . 'orders/archive':
			$response = OrderManager::getOrders(true);
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'orders/view/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			$response = OrderManager::getOrder( $m[1] );
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'orders/edit/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = OrderManager::updateOrder( $m[1], $request['data'] );
			} else {
				$response = OrderManager::getOrder( $m[1] );
			}
			break;

        case BASE_HUB_API_URI . 'orders/bulk-update' :
            if ( $request['method'] == 'POST' ) {
                $response = OrderManager::bulkUpdateOrder( $request['data'] );
            } else {
                $response = [ 'error' => 'wrong route' ];
            }
            break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'orders/delete/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = OrderManager::deleteOrder( $m[1] );
			} else {
				$response = [ 'error' => 'wrong route' ];
			}
			break;



		//customer

		case BASE_HUB_API_URI . 'customers/index':
			$response = CustomerManager::getCustomers();
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'customers/view/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			$response = CustomerManager::getCustomer( $m[1] );
			break;

		case ( preg_match( ',' . BASE_HUB_API_URI . 'customers/edit/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = CustomerManager::updateCustomer( $m[1], $request['data'] );
			} else {
				$response = CustomerManager::getCustomer( $m[1] );
			}
			break;
		case ( preg_match( ',' . BASE_HUB_API_URI . 'customers/delete/([0-9]+$),', $request['uri'], $m ) ? true : false ) :
			if ( $request['method'] == 'POST' ) {
				$response = CustomerManager::deleteCustomer( $m[1] );
			} else {
				$response = [ 'error' => 'wrong route' ];
			}
			break;


		//save settings

		case BASE_HUB_API_URI . 'settings/notification':
			if ( $request['method'] == 'POST' ) {
				$response = SettingsManager::saveSettings( $request['data'] );
			}else{
				$response = [ 'error' => 'wrong route' ];
			}
			break;

        //get currencies list
		case BASE_HUB_API_URI . 'settings/currencies/list':
			if ( $request['method'] == 'GET' ) {
				$response = SettingsManager::getCurrenciesList();
			}else{
				$response = [ 'error' => 'wrong route' ];
			}
			break;

		case BASE_HUB_API_URI . 'settings/payment_gateways/list':
			if ( $request['method'] == 'GET' ) {
				$response='12345';
				$response = SettingsManager::getPaymentGateways();
			}else{
				$response = [ 'error' => 'wrong route' ];
			}
			break;

		case BASE_HUB_API_URI . 'settings/shipping_methods/list':
			if ( $request['method'] == 'GET' ) {
				$response='12345';
				$response = SettingsManager::getShippingMethods();
			}else{
				$response = [ 'error' => 'wrong route' ];
			}
			break;

		case BASE_HUB_API_URI . 'settings/save':
			if ( $request['method'] == 'POST' ) {
				$response = SettingsManager::saveSettings( $request['data'] );
			}else{
				$response = [ 'error' => 'wrong route' ];
			}
			break;

        case BASE_HUB_API_URI . 'report/get':
            if( $request['method'] == 'POST') {
                $response = ReportManager::getReport($request['data']);
            }else{
                $response = ReportManager::getReport();
            }


            break;

        case BASE_HUB_API_URI . 'report/category':
            if( $request['method'] == 'POST') {
                $response = ReportManager::getCategoryReport($request['data']);
            }else{
                $response = ReportManager::getCategoryReport();
            }

            break;

		default:
			$response = [ 'error' => 'wrong route' ];
			break;
	}

	return $response;
}

function checkSecureKey( $token ) {

	if ( ! $token ) {
		return false;
	}

	$secure_key = get_option( 'hub_options' )['hub_secret_key'];

	$valid_token = md5( gmdate( 'Y' ) . $secure_key . gmdate( 'm' ) . gmdate( 'd' ) );

	if ( $token === $valid_token ) {
		return true;
	} else {
		return false;
	}
}
