<?php

add_action( 'woocommerce_new_order', 'event_new_order_placed');
add_action( 'woocommerce_order_status_pending', 'event_order_pending');
add_action( 'woocommerce_order_status_on-hold', 'event_order_onhold');

add_action( 'woocommerce_order_status_processing', 'event_order_processing');
add_action( 'woocommerce_order_status_completed', 'event_order_completed');
add_action( 'woocommerce_order_status_refunded', 'event_order_refunded');
add_action( 'woocommerce_order_status_cancelled', 'event_order_cancelled');
add_action( 'transition_post_status','event_product_published',10, 3);
add_action( 'woocommerce_product_set_stock', 'event_product_stock_updated');

function _getAddress(){
	$protocol=isset($_SERVER['HTTPS']) ? "https" : "http";
	return $protocol . "://".$_SERVER['HTTP_HOST'];
}

function event_new_order_placed($order_id){
	sendOrderCreatedRequest($order_id);
}
function event_order_pending($order_id){
	$status=pending;
	sendOrderStatusChangedRequest($order_id,$status);
}

function event_order_onhold($order_id){
	$status=onhold;
	sendOrderStatusChangedRequest($order_id,$status);
}
function event_order_processing($order_id){
	$status=processing;
	sendOrderStatusChangedRequest($order_id,$status);
}
function event_order_completed($order_id){
	$status=completed;
	sendOrderStatusChangedRequest($order_id,$status);
}
function event_order_refunded($order_id){
	$status=refunded;
	sendOrderStatusChangedRequest($order_id,$status);
}
function event_order_cancelled($order_id){
	$status=cancelled;
	sendOrderStatusChangedRequest($order_id,$status);
}
function event_product_published($new_status, $old_status, $post){
	if( 
        $old_status != 'publish' 
        && $new_status == 'publish' 
        && !empty($post->ID) 
        && in_array( $post->post_type, 
            array( 'product') 
            )
        ) {
          sendProductPublishedRequest();
     }
}

function event_product_stock_updated($product){
	$pr_id=$product->get_id();
	if(isset($pr_id)){
		sendProductStockUpdatedRequest($pr_id);
	}
}

function sendProductStockUpdatedRequest($product_id){
	$HUB_URL='http://devhub.funkyweb.biz';
	$API_PATH='/api/product/stock_updated/';
	$store_url=getAddress();

	$status=wp_safe_remote_post($HUB_URL.$API_PATH,array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('store_url'=>$store_url,'product_id'=>product_id),
			'cookies' => array()
    	)

	);
}

function sendProductPublishedRequest(){
	$HUB_URL='http://devhub.funkyweb.biz';
	$API_PATH='/api/product/published/';
	$store_url=getAddress();
	$status=wp_safe_remote_post($HUB_URL.$API_PATH,array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('store_url'=>$store_url),
			'cookies' => array()
    	)

	);
}

function sendOrderStatusChangedRequest($order_id,$new_status){
	$HUB_URL='http://devhub.funkyweb.biz';
	$API_PATH='/api/order/'.$order_id.'/change_status/';
	$store_url=getAddress();
	$status=wp_safe_remote_post($HUB_URL.$API_PATH,array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('order_id'=>$order_id,'store_url'=>$store_url,'new_status'=>$new_status),
			'cookies' => array()
    	)

	);
}

function sendOrderCreatedRequest($order_id){

	$HUB_URL='http://devhub.funkyweb.biz';
	$API_PATH='/api/order/register';
	$order=OrderManager::getOrder($order_id);
	$store_url=_getAddress();
	$status=wp_safe_remote_post($HUB_URL.$API_PATH,array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('order'=>$order,'store_url'=>$store_url),
			'cookies' => array()
    	)

	);
}
class TestNTF{
	public static function sendOCR($order_data){
		$HUB_URL='devhub.funkyweb.biz';
		$API_PATH='/api/order/register';
		$order=OrderManager::getOrder('10');
		$store_url=_getAddress();
		$status=wp_safe_remote_post($HUB_URL.$API_PATH,array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array('order'=>$order),
			'cookies' => array()
    	));
    	return $status;

	}
}
