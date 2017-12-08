<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class ProductManager
{

    public static function getProducts()
    {

        try {
            $products = array();
            $loop = new WP_Query([
//				'post_type'      => [ 'product', 'product_variation' ],
                'post_type' => ['product'],
                'posts_per_page' => -1
            ]);
            while ($loop->have_posts()) {
                $loop->the_post();
                $product = new WC_Product(get_the_ID());
                $data = $product->get_data();
                $categories_id = $product->get_category_ids();
                $tags_id = $product->get_tag_ids();
                $categories_name = [];
                $categories_tags = [];
                foreach ($categories_id as $cat_id) {
                    $categories_name[] = get_cat_name($cat_id);
                }
                foreach ($tags_id as $tag_id) {
                    $categories_tags[] = get_tag($tag_id)->name;
                }

                $products[] = [
                    'id' => $data['id'],
                    'image' => $product->get_image(),
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'stock_status' => $data['stock_status'],
                    'stock_quantity' => $data['stock_quantity'],
                    'price' => $data['price'],
                    'categories' => $categories_name,
                    'description' => $product->get_description(),
                    'tags' => $categories_tags,
                ];
            }
            $response = ['data' => $products, 'error' => ''];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into ProductManager::getProducts()'];
        }

        return $response;
    }

    public static function getProduct($id)
    {

        try {
            if (!$id) {

                $categories_temp = get_terms('product_cat');
                $categories = [];
                foreach ($categories_temp as $ct) {
                    $categories [$ct->slug] = $ct->name;
                }
                $data['all_categories'] = $categories;

            } else {
                $_pf = new WC_Product_Factory();
                $product = $_pf->get_product($id);
                $data = $product->get_data();

                $categories_id = $product->get_category_ids();
                $tags_id = $product->get_tag_ids();

                $categories_name = $categories_tags = $attributes = [];
                foreach ($categories_id as $cat_id) {
                    $categories_name[] = get_term_by('id', $cat_id, 'category')->slug;
                }
                foreach ($tags_id as $tag_id) {
                    $categories_tags[] = get_tag($tag_id)->name;
                }
                foreach ($product->get_attributes() as $attr) {
                    $attributes[] = [
                        'name' => $attr->get_name(),
                        'options' => $attr->get_options()
                    ];
                }

                $categories_temp = get_terms('product_cat');
                $categories = [];
                foreach ($categories_temp as $ct) {
                    $categories [$ct->slug] = $ct->name;
                }

                $data['all_categories'] = $categories;
                $data['image'] = $product->get_image();
                $data['categories'] = $categories_name;
                $data['tags'] = $categories_tags;
                $data['attributes'] = $attributes;

                $data['date_product'] = [
                    'created' => ($time = $product->get_date_created()) ? $time->getTimestamp() : null,
                    'last_modified' => ($time = $product->get_date_modified()) ? $time->getTimestamp() : null,
                    'date_on_sale_from' => ($time = $product->get_date_on_sale_from()) ? $time->getTimestamp() : null,
                    'date_on_sale_to' => ($time = $product->get_date_on_sale_to()) ? $time->getTimestamp() : null,
                ];
            }

            $response = ['data' => $data, 'error' => ''];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into ProductManager::getProducts()(id=' . $id . ')'];
        }

        return $response;
    }

    public static function updateProduct($id, $data)
    {
        try {
            $_pf = new WC_Product_Factory();
            $product = $_pf->get_product($id);
            $ct = [];
            foreach ($data['categories'] as $cat) {
                $cc = get_term_by('slug', $cat, 'product_cat');
                $ct [] = $cc->term_id;
            }

            if (isset($data['image_new_name'])) {
                if (!is_dir(wp_upload_dir()['basedir'] . '/hub_uploads')) {
                    mkdir(wp_upload_dir()['basedir'] . '/hub_uploads', 0775);
                }
                $new_image = wp_upload_dir()['basedir'] . '/hub_uploads/' . $data['image_new_name'];
                $new_image_url = wp_upload_dir()['url'] . '/hub_uploads/' . $data['image_new_name'];
                // open the output file for writing
                $ifp = fopen($new_image, 'wb');

                // split the string on commas
                // $data[ 0 ] == "data:image/png;base64"
                // $data[ 1 ] == <actual base64 string>
                $data_image = explode(',', $data['image_new_data']);

                // we could add validation here with ensuring count( $data ) > 1
                fwrite($ifp, base64_decode($data_image[1]));

                // clean up the file resource
                fclose($ifp);
                unset($data['image_new_name']);
                unset($data['image_new_data']);

                $filetype = wp_check_filetype(basename($new_image), null);

                $attachment = array(
                    'guid' => $new_image_url,
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_image)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment($attachment, $new_image, $id);

                if ($attach_id == 0) {
                    return ['data' => '', 'error' => 'problem upload new image'];
                }
                $product->set_image_id($attach_id);

            }

            foreach ($data as $prop => $val) {
                if ($prop == 'categories') {
                    $product->set_category_ids($ct);
                } else {
                    if ($prop == 'stock_quantity') {
                        if (!empty($val) || $val > 0) {
                            $product->set_manage_stock(true);
                            $product->set_stock_quantity($val);
                        }
                    } else {
                        $setter = "set_$prop";
                        $product->{$setter}($val);
                    }
                }
            }

            if ($product->save()) {
                $response = ['data' => ['product has updated'], 'error' => ''];
            } else {
                $response = ['data' => '', 'error' => 'product has not updated'];
            }

        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into ProductManager::updateProduct()'];
        }

        return $response;

    }

    public static function createProduct($data)
    {
        try {
            $data = $data['user_data'];
            $args = array(
                'post_author' => 1,
                'post_content' => '',
                'post_status' => "publish",
                'post_title' => $data['name'],
                'post_parent' => '',
                'post_type' => "product"
            );

            $post_id = wp_insert_post($args);


            $_pf = new WC_Product_Factory();
            $product = $_pf->get_product($post_id);
            $ct = [];
            foreach ($data['categories'] as $cat) {
                $cc = get_term_by('slug', $cat, 'product_cat');
                $ct [] = $cc->term_id;
            }

            if (isset($data['image_new_name'])) {
                if (!is_dir(wp_upload_dir()['basedir'] . '/hub_uploads')) {
                    mkdir(wp_upload_dir()['basedir'] . '/hub_uploads', 0775);
                }
                $new_image = wp_upload_dir()['basedir'] . '/hub_uploads/' . $data['image_new_name'];
                $new_image_url = wp_upload_dir()['url'] . '/hub_uploads/' . $data['image_new_name'];
                // open the output file for writing
                $ifp = fopen($new_image, 'wb');

                // split the string on commas
                // $data[ 0 ] == "data:image/png;base64"
                // $data[ 1 ] == <actual base64 string>
                $data_image = explode(',', $data['image_new_data']);

                // we could add validation here with ensuring count( $data ) > 1
                fwrite($ifp, base64_decode($data_image[1]));

                // clean up the file resource
                fclose($ifp);
                unset($data['image_new_name']);
                unset($data['image_new_data']);

                $filetype = wp_check_filetype(basename($new_image), null);

                $attachment = array(
                    'guid' => $new_image_url,
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_image)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                // Insert the attachment.
                $attach_id = wp_insert_attachment($attachment, $new_image, $id);

                if ($attach_id == 0) {
                    return ['data' => '', 'error' => 'problem upload new image'];
                }
                $product->set_image_id($attach_id);

            }

            foreach ($data as $prop => $val) {
                if ($prop == 'categories') {
                    $product->set_category_ids($ct);
                } else {
                    if ($prop == 'stock_quantity') {
                        if (!empty($val) || $val > 0) {
                            $product->set_manage_stock(true);
                            $product->set_stock_quantity($val);
                        }
                    } else {
                        $setter = "set_$prop";
                        $product->{$setter}($val);
                    }
                }
            }

//            return $product->save();


            if ($product->save()) {
                $response = ['data' => ['product has created'], 'error' => ''];
            } else {
                $response = ['data' => '', 'error' => 'product has not created'];
            }

        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Internal error into ProductManager::createProduct()'];
        }
        return $response;
    }

    public static function deleteProduct($id)
    {

        $_pf = new WC_Product_Factory();
        $product = $_pf->get_product($id);


        if ($product) {
            $response = ($product->delete()) ? ['data' => ['product deleted'], 'error' => ''] : [
                'data' => [],
                'error' => 'not delete'
            ];
        } else {
            $response = ['data' => [], 'error' => 'not delete'];
        }

        return $response;
    }
}