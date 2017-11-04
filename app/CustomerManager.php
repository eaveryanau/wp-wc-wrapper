<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */

Class CustomerManager {

	public static function getCustomers() {

		try {
			$data = [];
			$args = [
				'role' => 'customer'
			];

			$my_user_query = new WP_User_Query( $args );
			$customers     = $my_user_query->get_results();

			foreach ( $customers as $customer ) {
				$cc      = new WC_Customer( $customer->ID );
				$data [] = [
					'id'           => $cc->get_id(),
					'username'     => $cc->get_username(),
					'firstname'    => $cc->get_first_name(),
					'lastname'     => $cc->get_last_name(),
					'email'        => $cc->get_email(),
					'date_created' => $cc->get_date_created()->date( 'Y-m-d' ),
					'display_name' => $cc->get_display_name(),
					'phone'        => $cc->get_billing_phone(),
					'address'      => $cc->get_billing_address(),
					'order_count'  => $cc->get_order_count()
				];
			}
			$response = [ 'data' => $data, 'error' => '' ];
		} catch ( \Exception $ex ) {
			$response = [ 'data' => '', 'error' => 'Internal error into CustomerManager::getCustomers()' ];
		}

		return $response;
	}

	public static function getCustomer( $id ) {
		try {
			$customer = new WC_Customer( $id );
			if ( $customer->get_role() != 'customer' ) {
				return [ 'error' => 'customer not found' ];
			}
			$data                  = $customer->get_data();
			$data['date_created']  = $customer->get_date_created()->date( 'Y-m-d' );
			$data['date_modified'] = ( $customer->get_date_modified() ) ? $customer->get_date_modified()->date( 'Y-m-d' ) : null;

			$response = [ 'data' => $data, 'error' => '' ];
		} catch ( \Exception $ex ) {
			$response = [
				'data'  => '',
				'error' => 'Internal error into CustomerManager::getCustomer()(id=' . $id . ')'
			];
		}

		return $response;
	}

	public static function updateCustomer( $id, $data ) {


		$customer = new WC_Customer( $id );

		$customer->set_email($data['email']);
		$customer->set_first_name($data['first_name']);
		$customer->set_last_name($data['last_name']);
		$customer->set_role($data['role']);
		$customer->set_username($data['username']);

		// billing
		$customer->set_billing_first_name($data['billing']['first_name']);
		$customer->set_billing_last_name($data['billing']['last_name']);
		$customer->set_billing_company($data['billing']['company']);
		$customer->set_billing_address_1($data['billing']['address_1']);
		$customer->set_billing_address_2($data['billing']['address_2']);
		$customer->set_billing_city($data['billing']['city']);
		$customer->set_billing_state($data['billing']['state']);
		$customer->set_billing_postcode($data['billing']['postcode']);
		$customer->set_billing_country($data['billing']['country']);
		$customer->set_billing_email($data['billing']['email']);
		$customer->set_billing_phone($data['billing']['phone']);

		//shipping
		$customer->set_shipping_first_name($data['shipping']['first_name']);
		$customer->set_shipping_last_name($data['shipping']['last_name']);
		$customer->set_shipping_company($data['shipping']['company']);
		$customer->set_shipping_address_1($data['shipping']['address_1']);
		$customer->set_shipping_address_2($data['shipping']['address_2']);
		$customer->set_shipping_city($data['shipping']['city']);
		$customer->set_shipping_state($data['shipping']['state']);
		$customer->set_shipping_postcode($data['shipping']['postcode']);
		$customer->set_shipping_country($data['shipping']['country']);
		$customer->set_is_paying_customer($data['is_paying_customer']);
		$customer->save();

		return true;

	}

	public static function deleteCustomer($id){

		require_once(ABSPATH.'wp-admin/includes/user.php');

		$is_customer_delete  = wp_delete_user( $id );

		$response = ($is_customer_delete) ? ['data' => ['customer deleted'], 'error' => ''] : ['data' => [], 'error' => 'not delete'];

		return $response;
	}
}