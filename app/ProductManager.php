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

				$products[] = [
					'id'           => $data['id'],
					'image'        => $product->get_image(),
					'name'         => $data['name'],
					'sku'          => $data['sku'],
					'stock_status' => $data['stock_status'],
					'stock_quantity' => $data['stock_quantity'],
					'price'        => $data['price'],
					'categories'   => $categories_name,
					'description'   => $product->get_description(),
					'tags'         => $categories_tags,
                    /*'date_product' => [
                        'created'           => ($time = $product->get_date_created()) ? $time->getTimestamp() : null,
                        'last_modified'     => ($time = $product->get_date_modified()) ? $time->getTimestamp() : null,
                        'date_on_sale_from' => ($time = $product->get_date_on_sale_from()) ? $time->getTimestamp() : null,
                        'date_on_sale_to'   => ($time = $product->get_date_on_sale_to()) ? $time->getTimestamp() : null,
                    ]*/
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

            $categories_temp = get_terms('product_cat');
			$categories = [];
			foreach ($categories_temp as $ct){
                $categories [$ct->slug ] = $ct->name;
            }

            $data['all_categories'] = $categories;
			$data['image']      = $product->get_image();
			$data['categories'] = $categories_name;
			$data['tags']       = $categories_tags;
			$data['attributes'] = $attributes;

			$data['date_product'] = [
                'created'           => ($time = $product->get_date_created()) ? $time->getTimestamp() : null,
                'last_modified'     => ($time = $product->get_date_modified()) ? $time->getTimestamp() : null,
                'date_on_sale_from' => ($time = $product->get_date_on_sale_from()) ? $time->getTimestamp() : null,
                'date_on_sale_to'   => ($time = $product->get_date_on_sale_to()) ? $time->getTimestamp() : null,
            ];

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
        $ct = [];
        foreach($data['categories'] as $cat){
            $cc = get_term_by( 'slug', $cat, 'product_cat' );
            $ct []  = $cc->term_id;
        }

        $helper  = $product->get_data();
        foreach ( $helper as $prop => $val ) {
            if($prop == 'category_ids'){
                $product->set_category_ids($ct);
            }
            else {
                $setter = "set_$prop";
                $product->{$setter}($data[$prop]);
            }
        }

        return $product->save();

    }

	public static function deleteProduct( $id ) {

		$_pf      = new WC_Product_Factory();
		$product  = $_pf->get_product( $id );


		if ( $product ) {
			$response = ( $product->delete() ) ? [ 'data' => [ 'product deleted' ], 'error' => '' ] : [
				'data'  => [],
				'error' => 'not delete'
			];
		} else {
			$response = [ 'data' => [], 'error' => 'not delete' ];
		}

		return $response;
	}
}