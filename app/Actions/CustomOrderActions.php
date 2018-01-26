<?php
include_once '../Helper/OrderStatuses.php';
add_action("woocommerce_order_status_changed", "custom_order_status_action_trigger");

//add_action('woocommerce_order_status_wc-shipped','myfunc_2');


function custom_order_status_action_trigger($order_id){
    $order=wc_get_order($order_id);
    OrderStatuses::PlaceRelatedAction($order);
}
