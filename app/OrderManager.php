<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */

Class OrderManager {

	public static function getOrders() {

		try{
			$orders = array();

			$args = array(
//			'post_type'      => wc_get_order_types(),
				'post_type'      => [ 'shop_order' ],
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
					'ship'           => $order->get_formatted_shipping_address(),
					'customer_note'  => $order->get_customer_note(),
					'date'           => [
						'created'       => $date_created,
						'last_modified' => $date_last_modified,
						'paid'          => $date_paid,
						'completed'     => $date_complete
					],
					'total'          => $order->get_formatted_order_total(),
					'payment_method' => $order->get_payment_method_title()
				];
			}
			$response = [ 'data' => $orders, 'error' => '' ];
		}
		catch (\Exception $e){
			$response = [ 'data' => '', 'error' => 'Internal error into OrderManager::getOrders()' ];
		}

		return $response;
	}

	public static function getOrder( $id ) {

		try{
			$order = WC_Order_Factory::get_order( $id );

			// Items
			$items = $order->get_items();
			$itms  = [];
			foreach ( $items as $it ) {
				$itms[] = [
					'product_id' => $it->get_product_id(),
					'product_name' => $it->get_product( )->get_name(),
					'quantity'   => $it->get_quantity(),
					'sku' => $it->get_product( )->get_sku(),
					'subtotal' =>$it->get_subtotal(),
					'total' =>$it->get_total()
				];
			}
			// *End items


			// Metadata
			$meta = $order->get_meta_data();
			$mt   = [];
			foreach ( $meta as $meta_item ) {
				$mt [] = [
					'key'   => $meta_item->key,
					'value' => $meta_item->value
				];
			}
			// *End metadata

			//Shipping methods
			$order_shipping_methods = $order->get_shipping_methods();
			$shipping_methods = [];
			foreach ($order_shipping_methods as $it){
				$meta = [];
				foreach ($it->get_meta_data() as $meta_it){
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
			foreach ($order_fees as $it){
				$fees[] =[
					'name' => $it->get_name(),
					'total' => $it->get_total(),
					'total_tax' => $it->get_total_tax(),
					'tax_status' => $it->get_tax_status()
				];
			}
			// *End fees

			// Coupon
			$order_coupon = [];
			foreach ($order->get_data()['coupon_lines'] as $coupon){
				$order_coupon[] = [
					'code' => $coupon->get_code(),
					'discount' => $coupon->get_discount()
				];
			}
			// *End coupon

			// Date
			$date_created       = ( $order->get_date_created() ) ? $order->get_date_created()->getTimestamp() : null;
			$date_last_modified = ( $order->get_date_modified() ) ? $order->get_date_modified()->getTimestamp() : null;
			$date_paid          = ( $order->get_date_paid() ) ? $order->get_date_paid()->getTimestamp() : null;
			$date_complete      = ( $order->get_date_completed() ) ? $order->get_date_completed()->getTimestamp() : null;
			// *End date

			$order               = $order->get_data();

			$order['date_order']       = [
				'created'       => $date_created,
				'last_modified' => $date_last_modified,
				'paid'          => $date_paid,
				'completed'     => $date_complete
			];
			$order['line_items'] = $itms;
			$order['meta_data']  = $mt;
			$order['shipping_lines'] = $shipping_methods;
			$order['fee_lines'] = $fees;
			$order['coupon_lines'] = $order_coupon;

			$response = [ 'data' => $order, 'error' => '' ];
		}
		catch (\Exception $e){
			$response = [ 'data' => '', 'error' => 'Internal error into OrderManager::getOrder() (id='. $id .')' ];
		}

		return $response;
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