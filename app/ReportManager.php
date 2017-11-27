<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class ReportManager
{

    public static function getReport($data = null)
    {

//        wc-cancelled
        try {
            $complete_orders = array();
            $canceled_orders = array();
            $total = 0;

            if ($data) {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-completed'],
                    'posts_per_page' => '-1',
                    'date_query' => array(
                        array(
//                            2015-02-17 23:59:59
                            'after' => date('Y-m-d H:i:s', $data['start']),
                            'before' => date('Y-m-d H:i:s', $data['end']),
                            'inclusive' => true
                        )
                    )
                );
            } else {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-completed'],
                    'posts_per_page' => '-1',
                );

            }

            $loop1 = new WP_Query($args);

            while ($loop1->have_posts()) {
                $loop1->the_post();
                $order = new WC_Order(get_the_ID());
                $orders [] = $order;
                $total += $order->get_total();

                $date_format = 'Y-m-d';

                $complete_orders[$order->get_date_completed()->date($date_format)] =
                    (isset($complete_orders[$order->get_date_completed()->date($date_format)])) ?
                        $complete_orders[$order->get_date_completed()->date($date_format)] + 1 : 1;

            }
            wp_reset_postdata();

            // wc-cancelled orders
            if ($data) {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-cancelled'],
                    'posts_per_page' => '-1',
                    'date_query' => array(
                        array(
//                            2015-02-17 23:59:59
                            'after' => date('Y-m-d H:i:s', $data['start']),
                            'before' => date('Y-m-d H:i:s', $data['end']),
                            'inclusive' => true
                        )
                    )
                );
            } else {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-cancelled'],
                    'posts_per_page' => '-1',

                );
            }


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
                'role' => 'customer',
                'date_query' => array(
                    array(
//                            2015-02-17 23:59:59
                        'after' => date('Y-m-d H:i:s', $data['start']),
                        'before' => date('Y-m-d H:i:s', $data['end']),
                        'inclusive' => true
                    )
                )
            ];

            $my_user_query = new WP_User_Query($args);
            $customers = $my_user_query->get_results();

            $data_temp = [];
            $data_temp[] = [
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
                ]];

            $data_temp = [
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
                ]
            ];

            $response = [
                'data' => $data_temp,
                'error' => ''
            ];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into OrderManager::getOrders()'];
        }

        return $response;

    }

    public static function getCategoryReport($data = null)
    {
        try {
            if ($data) {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-completed'],
                    'posts_per_page' => '-1',
                    'date_query' => array(
                        array(
//                            2015-02-17 23:59:59
                            'after' => date('Y-m-d H:i:s', $data['start']),
                            'before' => date('Y-m-d H:i:s', $data['end']),
                            'inclusive' => true
                        )
                    )
                );
            } else {
                $args = array(
//			        'post_type'      => wc_get_order_types(),
                    'post_type' => ['shop_order'],
                    'post_status' => ['wc-completed'],
                    'posts_per_page' => '-1',
                );

            }


            $categories = [];
            $response = [];
            $orders = [];
            $loop1 = new WP_Query($args);

            $all_cat = [];
            $date_format = 'Y-m-d';
            while ($loop1->have_posts()) {
                $loop1->the_post();
                $order = new WC_Order(get_the_ID());
                $items = $order->get_items();
                $itms = [];
                $arr_terms_id = [];
                foreach ($items as $it) {
                    $terms = get_the_terms($it->get_product_id(), 'product_cat');

//                $arr_terms_id = [];
                    foreach ($terms as $term) {
                        $all_cat[] = $term->term_id;
                        $arr_terms_id[] = $term->term_id;
//                    if (!in_array($term->term_id, $categories)) {
                        $flag = true;
                        foreach ($categories as $c) {
                            if ($c['id'] == $term->term_id) {
                                $flag = false;
                            }
                        }
                        if ($flag) {
                            $categories[] =
                                [
                                    'id' => $term->term_id,
                                    'name' => $term->name,
                                    'count' => 0,
                                    'date' => null,
                                ];
                        }

                    }
                    $itms = array_unique($arr_terms_id);
                }

                $orders [] = [
                    'id' => $order->get_id(),
                    'total' => $order->get_total(),
                    'items' => $itms,
                    'date' => $order->get_date_completed()->date($date_format)
                ];
            }


            $i = 0;
            foreach ($categories as $category) {
                foreach ($orders as $or) {
                    if (in_array($category['id'], $or['items'])) {
                        $category['count'] += 1;
                        $category['total'] += $or['total'];
                        if (isset($category['date'][$or['date']])) {
                            $category['date'][$or['date']] += 1;
                        } else {
                            $category['date'][$or['date']] = 1;
                        }
                    } else {
//                    $category['count'] += 1;
                        $category['date'][$or['date']] = 0;
                    }
                }
                ksort($category['date']);
                $response[] = $category;
                $i++;
                if ($i == 5) {
                    break;
                }
            }

            $t = true;
            while ($t) {
                $t = false;
                for ($i = 0; $i < count($response) - 1; $i++) {
                    if ($response[$i]['count'] < $response[$i + 1]['count']) {
                        $temp = $response[$i + 1];
                        $response[$i + 1] = $response[$i];
                        $response[$i] = $temp;
                        $t = true;
                    }
                }
            }

            $out_response = ['data' => $response, 'error' => ''];
        } catch (\Exception $e) {
            $out_response = ['data' => '', 'error' => 'Internal error into OrderManager::getOrders()'];
        }
        return $out_response;
    }

}

