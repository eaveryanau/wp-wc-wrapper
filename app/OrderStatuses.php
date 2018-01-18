<?php
include_once 'Helper/OrderStatuses.php';

function register_custom_order_statuses() {
    register_post_status( OrderStatuses::VERIFIED, array(
        'label'                     => _x('Verified & Approved', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Verified & Approved <span class="count">(%s)</span>', 'Verified & Approved <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::VERIFICATION_REQUIRED, array(
        'label'                     => _x('Verification Required', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Verification Required <span class="count">(%s)</span>', 'Verification Required <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::VERIFICATION_UNSUCCESSFUL, array(
        'label'                     => _x('Verification Unsuccessful', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Verification Unsuccessful <span class="count">(%s)</span>', 'Verification Unsuccessful <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::PACKING, array(
        'label'                     => _x('Packing', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Awaiting shipment <span class="count">(%s)</span>', 'Awaiting shipment <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::SHIPPED, array(
        'label'                     => _x('Shipped', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::ON_HOLD_STOCK_UNAVAILABLE, array(
        'label'                     => _x('On Hold - Stock Unavailable', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'On Hold - Stock Unavailable <span class="count">(%s)</span>', 'On Hold - Stock Unavailable <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::RETURN_REQUESTED, array(
        'label'                     => _x('Return Requested', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Return Requested <span class="count">(%s)</span>', 'Return Requested <span class="count">(%s)</span>' )
    ) );


    register_post_status( OrderStatuses::ON_HOLD_CUSTOMER_RETURN, array(
        'label'                     => _x('On Hold - Customer Return', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'On Hold - Customer Return <span class="count">(%s)</span>', 'On Hold - Customer Return <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::ORDER_RETURNED_TO_WAREHOUSE, array(
        'label'                     => _x('Order Returned to Warehouse', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Order Returned to Warehouse <span class="count">(%s)</span>', 'Order Returned to Warehouse <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::PRODUCT_RETURNED_TO_COURIER, array(
        'label'                     => _x('Product returned to the courier straight away', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Delivery Failed <span class="count">(%s)</span>', 'Delivery Failed <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::PROBLEMS_OF_COURIER_SERVICE, array(
        'label'                     => _x('Problems of Courier service', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Delivery Failed <span class="count">(%s)</span>', 'Delivery Failed <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::SUCCESSFUL_RETURN_FROM_CUSTOMER, array(
        'label'                     => _x('Successful return from customer', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Delivery Failed <span class="count">(%s)</span>', 'Delivery Failed <span class="count">(%s)</span>' )
    ) );

    register_post_status( OrderStatuses::AWAITING_DISPATCH, array(
        'label'                     => _x( 'Awaiting Dispatch', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Awaiting Dispatch <span class="count">(%s)</span>', 'Awaiting Dispatch<span class="count">(%s)</span>', 'woocommerce' )
    ) );

    register_post_status( OrderStatuses::DELIVERED, array(
        'label'                     => _x( 'Delivery (successful)', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Delivery (successful) <span class="count">(%s)</span>', 'Delivery (successful)<span class="count">(%s)</span>', 'woocommerce' )
    ) );

    /*fail statuses*/

    register_post_status( OrderStatuses::CANCELED_UNVERIFIED, array(
        'label'                     => _x('Cancelled Unverified', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Cancelled Unverified <span class="count">(%s)</span>', 'Cancelled Unverified <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::CANCELED_STOCK_UNAVAILABLE, array(
        'label'                     => _x('Cancelled Stock Unavailable', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Cancelled Stock Unavailable <span class="count">(%s)</span>', 'Cancelled Stock Unavailable <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::CANCELED_RETURNED, array(
        'label'                     => _x('Cancelled Returned', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Cancelled Returned <span class="count">(%s)</span>', 'Cancelled Returned <span class="count">(%s)</span>' )
    ) );
    register_post_status( OrderStatuses::DELIVERY_FAILED, array(
        'label'                     => _x('Delivery Failed', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Delivery Failed <span class="count">(%s)</span>', 'Delivery Failed <span class="count">(%s)</span>' )
    ) );


}
add_action( 'init', 'register_custom_order_statuses' );

// Add to list of WC Order statuses
function add_custom_statuses_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    $new_order_statuses[OrderStatuses::ON_HOLD_CUSTOMER_RETURN] = _x('On Hold - Customer Return', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::ON_HOLD_STOCK_UNAVAILABLE] = _x('On Hold - Stock Unavailable', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::PACKING] = _x('Packing', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::RETURN_REQUESTED] = _x('Return Requested', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::SHIPPED] = _x('Shipped', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::VERIFIED] = _x('Verified & Approved', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::VERIFICATION_REQUIRED] = _x('Verification Required', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::VERIFICATION_UNSUCCESSFUL] = _x('Verification Unsuccessful', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::ORDER_RETURNED_TO_WAREHOUSE] = _x('Order Returned to Warehouse', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::PRODUCT_RETURNED_TO_COURIER] = _x('Product Returned to the Courier Straight Away', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::PROBLEMS_OF_COURIER_SERVICE] = _x('Problems of Courier Service', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::SUCCESSFUL_RETURN_FROM_CUSTOMER] = _x('Successful Return From Customer', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::DELIVERED]=_x('Delivery (successful)', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::AWAITING_DISPATCH] = _x( 'Awaiting Dispatch', 'Order status', 'woocommerce' );

    /*fail statuses*/
    $new_order_statuses[OrderStatuses::CANCELED_UNVERIFIED] = _x('Cancelled Unverified', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::CANCELED_STOCK_UNAVAILABLE] = _x('Cancelled Stock Unavailable', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::CANCELED_RETURNED] = _x('Cancelled Returned', 'Order status', 'woocommerce' );
    $new_order_statuses[OrderStatuses::DELIVERY_FAILED] = _x('Delivery Failed', 'Order status', 'woocommerce' );

    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

    }


    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_custom_statuses_to_order_statuses' );