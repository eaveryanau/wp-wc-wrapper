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
					'display_name' => $cc->get_display_name()
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

		$helper   = self::getCustomer( $id );
		$customer = new WC_Customer( $id );
		foreach ( $helper as $prop => $val ) {
			$setter = "set_$prop";
			$customer->{$setter}( $data[ $prop ] );
		}

		return $customer->save();
	}
}