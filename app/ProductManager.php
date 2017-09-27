<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class ProductManager {

	public static function getProducts() {

		try{
			$products = array();
			$loop     = new WP_Query( [
				'post_type'      => [ 'product', 'product_variation' ],
				'posts_per_page' => - 1
			] );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$product         = new WC_Product( get_the_ID() );
				$data            = $product->get_data();
				$categories_id   = $product->get_category_ids();
				$tags_id         = $product->get_tag_ids();
				$categories_name = [];
				$categories_tags = [];
				foreach ( $categories_id as $cat_id ) {
					$categories_name[] = get_cat_name( $cat_id );
				}
				foreach ( $tags_id as $tag_id ) {
					$categories_tags[] = get_tag( $tag_id )->name;
				}
				$date       = $product->get_date_created()->date('Y-m-d');
				$products[] = [
					'id'           => $data['id'],
					'image'        => $product->get_image(),
					'name'         => $data['name'],
					'sku'          => $data['sku'],
					'stock_status' => $data['stock_status'],
					'price'        => $data['price'],
					'categories'   => $categories_name,
					'tags'         => $categories_tags,
					'date'         => $date
				];
			}
			$response = [ 'data' => $products, 'error' => '' ];
		}
		catch (\Exception $e){
			$response = [ 'data' => '', 'error' => 'Internal error into ProductManager::getProducts()' ];
		}

		return $response;
	}

	public static function getProduct( $id ) {

		try{
			$_pf      = new WC_Product_Factory();
			$product  = $_pf->get_product( $id );
			$data = $product->get_data();

			$categories_id = $product->get_category_ids();
			$tags_id       = $product->get_tag_ids();

			$categories_name = $categories_tags = $attributes = [];
			foreach ( $categories_id as $cat_id ) {
				$categories_name[] = get_cat_name( $cat_id );
			}
			foreach ( $tags_id as $tag_id ) {
				$categories_tags[] = get_tag( $tag_id )->name;
			}
			foreach ( $product->get_attributes() as $attr ) {
				$attributes[] = [
					'name'    => $attr->get_name(),
					'options' => $attr->get_options()
				];
			}

			$data['image']      = $product->get_image();
			$data['categories'] = $categories_name;
			$data['tags']       = $categories_tags;
			$data['attributes'] = $attributes;

			$response = [ 'data' => $data, 'error' => '' ];
		}
		catch (\Exception $e){
			$response = [ 'data' => '', 'error' => 'Internal error into ProductManager::getProducts()(id='. $id .')' ];
		}

		return $response;
	}

	public static function updateProduct( $id, $data ) {

		$_pf     = new WC_Product_Factory();
		$product = $_pf->get_product( $id );
		$helper  = $product->get_data();
		foreach ( $helper as $prop => $val ) {
			$setter = "set_$prop";
			$product->{$setter}( $data[ $prop ] );
		}

		return $product->save();

	}
}