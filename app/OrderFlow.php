<?php
include_once 'Helper/OrderStatuses.php';
add_action( 'woocommerce_after_payfort_payment', 'payfort_success_set_order_status',100 );
add_filter('woocommerce_cod_process_payment_order_status','cod_prepare_new_orders',100,2);

function cod_prepare_new_orders($var, $order){

    $verified=false;//Todo verification process

    $verification_failed=false;//Todo verification process

    if(!empty($order)){
        if(!$verified){
            $var=OrderStatuses::VERIFICATION_REQUIRED;
        }else{
            $var=OrderStatuses::VERIFIED;
        }
        if($verification_failed){
            $var=OrderStatuses::VERIFICATION_UNSUCCESSFUL;
        }
    }

    return $var;
}

/**
 * @param WC_Order $order
 */
function payfort_success_set_order_status($order){

     if(!empty($order)) {
        $order->update_status(OrderStatuses::VERIFIED);
    }
}
