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

        $products = array();
        $loop = new WP_Query([
            'post_type' => ['product', 'product_variation'],
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
            $date = $product->get_date_created()->date('Y-m-d');
            $products[] = [
                'id' => $data['id'],
                'image' => $product->get_image(),
                'name' => $data['name'],
                'sku' => $data['sku'],
                'stock_status' => $data['stock_status'],
                'price' => $data['price'],
                'categories' => $categories_name,
                'tags' => $categories_tags,
                'date' => $date
            ];
        }

        return $products;
    }
}