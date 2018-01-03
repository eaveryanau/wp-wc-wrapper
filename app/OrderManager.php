<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class OrderManager
{

    public static function getOrders($archive = false, $date_filter = null)
    {
        try {
            $orders = array();

            if (isset($date_filter['filter'])) {

                $after = isset($date_filter['filter']['date_start']) ? $date_filter['filter']['date_start'] : 0;
                $before = isset($date_filter['filter']['date_end']) ? $date_filter['filter']['date_end'] . '23:59:59' : date('Y-m-d H:j:s');

                if ($archive) {
                    $args = array(
//			        'post_type'      => wc_get_order_types(),
                        'post_type' => ['shop_order'],
                        'post_status' => 'trash',
                        'posts_per_page' => '-1',
                        'date_query' => array(
                            array(
//                            2015-02-17 23:59:59
                                'after' => $after,
                                'before' => $before,
                                'inclusive' => true
                            )
                        )
                    );
                } else {
                    $args = array(
//			        'post_type'      => wc_get_order_types(),
                        'post_type' => ['shop_order'],
                        'post_status' => array_keys(wc_get_order_statuses()),
                        'posts_per_page' => '-1',
                        'date_query' => array(
                            array(
//                            2015-02-17 23:59:59
                                'after' => $after,
                                'before' => $before,
                                'inclusive' => true
                            )
                        )
                    );
                }

            } else {

                if ($archive) {
                    $args = array(
//			        'post_type'      => wc_get_order_types(),
                        'post_type' => ['shop_order'],
                        'post_status' => 'trash',
                        'posts_per_page' => '-1'
                    );
                } else {
                    $args = array(
//			        'post_type'      => wc_get_order_types(),
                        'post_type' => ['shop_order'],
                        'post_status' => array_keys(wc_get_order_statuses()),
                        'posts_per_page' => '-1'
                    );
                }

            }

            $loop = new WP_Query($args);

            while ($loop->have_posts()) {
                $loop->the_post();
                $order = new WC_Order(get_the_ID());

                $date_format = 'Y-m-d H:j:s';
                $date_created = ($order->get_date_created()) ? $order->get_date_created()->date($date_format) : null;
                $date_last_modified = ($order->get_date_modified()) ? $order->get_date_modified()->date($date_format) : null;
                $date_paid = ($order->get_date_paid()) ? $order->get_date_paid()->date($date_format) : null;
                $date_complete = ($order->get_date_completed()) ? $order->get_date_completed()->date($date_format) : null;

                // Items
                $items = $order->get_items();
                $itms = [];
                foreach ($items as $it) {
                    $itms[] = [
                        'product_id' => $it->get_product_id(),
                        'product_name' => $it->get_product()->get_name(),
                        'quantity' => $it->get_quantity(),
                        'sku' => $it->get_product()->get_sku(),
                        'subtotal' => $it->get_subtotal(),
                        'total' => $it->get_total()
                    ];
                }
                // *End items

                $orders[] = [
                    'id' => get_the_ID(),
                    'status' => $order->get_status(),
                    'user_id' => $order->get_user_id(),
                    'user_role' => $order->get_user()->roles[0],
                    'billing' => [
                        'first_name' => $order->get_billing_first_name(),
                        'last_name' => $order->get_billing_last_name(),
                        'email' => $order->get_billing_email()
                    ],
                    'ship' => $order->get_formatted_shipping_address(),
                    'customer_note' => $order->get_customer_note(),
                    'date' => $date_created,
                    'total' => $order->get_formatted_order_total(),
                    'payment_method' => $order->get_payment_method_title(),
                    'items' => $itms
                ];
            }
            $response = ['data' => $orders, 'error' => ''];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into OrderManager::getOrders()'];
        }

        return $response;
    }

    public static function getOrder($id)
    {

        try {
            $order = WC_Order_Factory::get_order($id);

            // Items
            $items = $order->get_items();
            $itms = [];
            foreach ($items as $it) {
                $itms[] = [
                    'order_item_id' => $it->get_id(),
                    'product_id' => $it->get_product_id(),
                    'product_name' => $it->get_product()->get_name(),
                    'quantity' => $it->get_quantity(),
                    'sku' => $it->get_product()->get_sku(),
                    'subtotal' => $it->get_subtotal(),
                    'total' => $it->get_total()
                ];
            }
            // *End items


            // Metadata
            $meta = $order->get_meta_data();
            $mt = [];
            foreach ($meta as $meta_item) {
                $mt [] = [
                    'id' => $meta_item->id,
                    'key' => $meta_item->key,
                    'value' => $meta_item->value
                ];
            }
            // *End metadata

            //Shipping methods
            $order_shipping_methods = $order->get_shipping_methods();
            $shipping_methods = [];
            foreach ($order_shipping_methods as $it) {
                $meta = [];
                foreach ($it->get_meta_data() as $meta_it) {
                    $meta [] = [
                        'key' => $meta_it->key,
                        'value' => $meta_it->value
                    ];
                }
                $shipping_methods[] = [
                    'method' => $it->get_method_id(),
                    'meta_data' => $meta,
                    'total' => $it->get_total(),
                    'total_tax' => $it->get_total_tax()
                ];
            }
            // *End shipping methods

            // Fee items
            $order_fees = $order->get_fees();
            $fees = [];
            foreach ($order_fees as $it) {
                $fees[] = [
                    'name' => $it->get_name(),
                    'total' => $it->get_total(),
                    'total_tax' => $it->get_total_tax(),
                    'tax_status' => $it->get_tax_status()
                ];
            }
            // *End fees

            // Coupon
            $order_coupon = [];
            foreach ($order->get_data()['coupon_lines'] as $coupon) {
                $order_coupon[] = [
                    'code' => $coupon->get_code(),
                    'discount' => $coupon->get_discount()
                ];
            }
            // *End coupon

            // Date
            $date_created = ($order->get_date_created()) ? $order->get_date_created()->getTimestamp() : null;
            $date_last_modified = ($order->get_date_modified()) ? $order->get_date_modified()->getTimestamp() : null;
            $date_paid = ($order->get_date_paid()) ? $order->get_date_paid()->getTimestamp() : null;
            $date_complete = ($order->get_date_completed()) ? $order->get_date_completed()->getTimestamp() : null;
            // *End date

            $total = $order->get_total();
            $order = $order->get_data();

            $order['date_order'] = [
                'created' => $date_created,
                'last_modified' => $date_last_modified,
                'paid' => $date_paid,
                'completed' => $date_complete
            ];
            $order['line_items'] = $itms;
            $order['meta_data'] = $mt;
            $order['shipping_lines'] = $shipping_methods;
            $order['fee_lines'] = $fees;
            $order['coupon_lines'] = $order_coupon;
            $order['total'] = $total;

            $response = ['data' => $order, 'error' => ''];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into OrderManager::getOrder() (id=' . $id . ')'];
        }

        return $response;
    }

    public static function bulkUpdateOrder($data)
    {
        //update only status (bulk action)

        try {
            foreach ($data['data'] as $order_id) {
                $order = WC_Order_Factory::get_order($order_id);
                $order->set_status($data['status']);
                $order->save();
            }

            return ['data' => ['Orders updated']];

        } catch (\Exception $e) {
            return ['error' => ['Orders not updated']];
        }


    }

    public static function updateOrder($id, $data)
    {
        try {
            $order = WC_Order_Factory::get_order($id);

            if (self::setAttribute($order, $data)) {
                $response = ['data' => ['Order updated'], 'error' => ''];
            } else {
                $response = ['data' => '', 'error' => 'Order not updated'];
            }
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into OrderManager::updateOrder() (id=' . $id . ')'];
        }


        return $response;

    }

    public static function setAttribute($order, $data)
    {

        try {
            $order->set_status($data['status']);
//            $order->set_currency($data['currency']);
//            $order->set_discount_total($data['discount_total']);
//            $order->set_discount_tax($data['discount_tax']);
//            $order->set_shipping_total($data['shipping_total']);
//            $order->set_shipping_tax($data['shipping_tax']);
//            $order->set_cart_tax($data['cart_tax']);
//            $order->set_total($data['total']);

            // billing
//            $order->set_billing_first_name($data['billing']['first_name']);
//            $order->set_billing_last_name($data['billing']['last_name']);
//            $order->set_billing_company($data['billing']['company']);
//            $order->set_billing_address_1($data['billing']['address_1']);
//            $order->set_billing_address_2($data['billing']['address_2']);
//            $order->set_billing_city($data['billing']['city']);
//            $order->set_billing_state($data['billing']['state']);
//            $order->set_billing_postcode($data['billing']['postcode']);
//            $order->set_billing_country($data['billing']['country']);
//            $order->set_billing_email($data['billing']['email']);
//            $order->set_billing_phone($data['billing']['phone']);

            //items
            $order_line_items = $order->get_items();
            foreach ($order_line_items as $old_it) {
                $hold_item = false;
                foreach ($data['line_items'] as $new_it) {
                    if ($old_it->get_id() == $new_it['order_item_id']) {
                        $hold_item = true;
                        break;
                    }
                }
                if (!$hold_item) {
                    $order->remove_item($old_it->get_id());
                }
            }
            $order->calculate_totals();

            //shipping
//            $order->set_shipping_first_name($data['shipping']['first_name']);
//            $order->set_shipping_last_name($data['shipping']['last_name']);
//            $order->set_shipping_company($data['shipping']['company']);
//            $order->set_shipping_address_1($data['shipping']['address_1']);
//            $order->set_shipping_address_2($data['shipping']['address_2']);
//            $order->set_shipping_city($data['shipping']['city']);
//            $order->set_shipping_state($data['shipping']['state']);
//            $order->set_shipping_postcode($data['shipping']['postcode']);
//            $order->set_shipping_country($data['shipping']['country']);

//            $order->set_payment_method($data['payment_method']);
//            $order->set_payment_method_title($data['payment_method_title']);
//            $order->set_transaction_id($data['transaction_id']);
//            $order->set_customer_ip_address($data['customer_ip_address']);
//            $order->set_customer_user_agent($data['customer_user_agent']);
//            $order->set_created_via($data['created_via']);
//            $order->set_customer_note($data['customer_note']);

            //metadata
//            $order->set_meta_data($data['meta_data']);

            //Date

//            $order->set_date_completed($data['date_order']['completed']);
//            $order->set_date_paid($data['date_order']['paid']);
//            $order->set_date_created($data['date_order']['created']);
//            $order->set_date_modified($data['date_order']['modified']);
            $order->save();

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteOrder($id)
    {

        $order = WC_Order_Factory::get_order($id);


        if ($order) {
            $response = ($order->delete()) ? ['data' => ['order deleted'], 'error' => ''] : [
                'data' => [],
                'error' => 'not delete'
            ];
        } else {
            $response = ['data' => [], 'error' => 'not delete'];
        }

        return $response;
    }
}

