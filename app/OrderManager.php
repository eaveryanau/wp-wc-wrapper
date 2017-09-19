<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */

Class OrderManager {

	public static function getOrders() {

		$orders = array();

		$args = array(
			'post_type'      => wc_get_order_types(),
			'post_status'    => array_keys( wc_get_order_statuses() ),
			'posts_per_page' => '-1'
		);
		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) {
			$loop->the_post();
			$order = new WC_Order( get_the_ID() );

			$date_format        = 'Y-m-d H:j:s';
			$date_created       = ( $order->get_date_created() ) ? $order->get_date_created()->date( $date_format ) : null;
			$date_last_modified = ( $order->get_date_modified() ) ? $order->get_date_modified()->date( $date_format ) : null;
			$date_paid          = ( $order->get_date_paid() ) ? $order->get_date_paid()->date( $date_format ) : null;
			$date_complete      = ( $order->get_date_completed() ) ? $order->get_date_completed()->date( $date_format ) : null;

			$orders[] = [
				'id'             => get_the_ID(),
				'status'         => $order->get_status(),
				'billing'        => [
					'first_name' => $order->get_billing_first_name(),
					'last_name'  => $order->get_billing_last_name(),
					'email'      => $order->get_billing_email()
				],
				'ship'           => $order->get_shipping_total(),
				'customer_note'  => $order->get_customer_note(),
//			    'order_note'
				'date'           => [
					'created'       => $date_created,
					'last_modified' => $date_last_modified,
					'paid'          => $date_paid,
					'complete'      => $date_complete
				],
				'total'          => $order->get_total(),
				'payment_method' => $order->get_payment_method_title()
			];
		}

		return $orders;
	}

	public static function getOrder( $id ) {

		$order = WC_Order_Factory::get_order( $id );

		return $order->get_data();
	}

	public static function updateOrder( $id, $data ) {

		$order  = WC_Order_Factory::get_order( $id );
		$helper = self::getOrder( $id );
		foreach ( $helper as $prop => $val ) {
			$setter = "set_$prop";
			$order->{$setter}( $data[ $prop ] );
		}

		return $order->save();

	}
}