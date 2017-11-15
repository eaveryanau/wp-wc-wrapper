<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class ReportManager
{

    public static function getReport()
    {
//        wc-cancelled
        try {
            $complete_orders = array();
            $canceled_orders = array();
            $total = 0;

            // complete orders
            $args = array(
//			        'post_type'      => wc_get_order_types(),
                'post_type' => ['shop_order'],
                'post_status' => ['wc-completed'],
                'posts_per_page' => '-1'
            );

            $loop1 = new WP_Query($args);

            while ($loop1->have_posts()) {
                $loop1->the_post();
                $order = new WC_Order(get_the_ID());
                $total += $order->get_total();

                $date_format = 'Y-m-d';

                $complete_orders[$order->get_date_completed()->date($date_format)] =
                    (isset($complete_orders[$order->get_date_completed()->date($date_format)])) ?
                        $complete_orders[$order->get_date_completed()->date($date_format)] + 1 : 1;

            }

            wp_reset_postdata();

            // wc-cancelled orders
            $args = array(
//			        'post_type'      => wc_get_order_types(),
                'post_type' => ['shop_order'],
                'post_status' => ['wc-cancelled'],
                'posts_per_page' => '-1'
            );

            $loop2 = new WP_Query($args);
            while ($loop2->have_posts()) {
                $loop2->the_post();
                $order = new WC_Order(get_the_ID());

                $date_format = 'Y-m-d';

                $canceled_orders[$order->get_date_modified()->date($date_format)] =
                    (isset($canceled_orders[$order->get_date_modified()->date($date_format)])) ?
                        $canceled_orders[$order->get_date_modified()->date($date_format)] + 1 : 1;

            }

//            wp_reset_postdata();

            //customers per month
            $args = [
                'role' => 'customer',
                'date_query' => array(
                    array('after' => '1 month ago', 'inclusive' => true)
                )
            ];

            $my_user_query = new WP_User_Query($args);
            $customers_per_month = $my_user_query->get_results();

            //all customers
            $args = [
                'role' => 'customer'
            ];

            $my_user_query = new WP_User_Query($args);
            $customers = $my_user_query->get_results();


            $response = [
                'data' => [
                    'orders' => [
                        'all' => [
                            'completed' => $complete_orders,
                            'canceled' => $canceled_orders,
                            'total' => $total
                        ],
                        'per_month' => [
                            'completed' => $complete_orders,
                            'canceled' => $canceled_orders,
                        ]

                    ],
                    'customers' => [
                        'all' => count($customers),
                        'per_month' => count($customers_per_month)
                    ],
                ],
                'error' => ''
            ];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into OrderManager::getOrders()'];
        }

        return $response;

    }

}

