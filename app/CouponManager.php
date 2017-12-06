<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 11/20/17
 * Time: 11:38 AM
 */
Class CouponManager
{

    public static function getAllCoupon()
    {

        try {
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'shop_coupon',
                'post_status' => 'publish',
            );

            $coupons = get_posts($args);

            $data = [];
            foreach ($coupons as $it) {
                $coupon = new WC_Coupon($it->post_title);
                $data[] = [
                    'id' => $it->ID,
                    'code' => $coupon->get_code(),
                    'amount' => $coupon->get_amount(),
                    'type' => $coupon->get_discount_type()
                ];
            }

            $response = ['data' => $data, 'error' => ''];
        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into CouponManager::getAllCoupon()'];
        }

        return $response;
    }

    public static function getCoupon($id)
    {
        try {
            $post = get_post($id);
            if ($post) {
                $coupon = new WC_Coupon($post->post_title);
                $data = [
                    'id' => $post->ID,
                    'code' => $coupon->get_code(),
                    'amount' => $coupon->get_amount(),
                    'type' => $coupon->get_discount_type()
                ];
                $response = ['data' => $data, 'error' => ''];
            } else {
                $response = ['data' => '', 'error' => 'Not found post CouponManager::getCoupon()'];
            }

        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into CouponManager::getCoupon()'];
        }
        return $response;
    }

    public static function updateCoupon($id, $data)
    {
        try {
            $post = get_post($id);
            if ($post) {
                $coupon = new WC_Coupon($post->post_title);
                $coupon->set_amount($data['amount']);
                $coupon->set_code($data['code']);
                if ($coupon->save()) {
                    $response = ['data' => ['successfully'], 'error' => ''];
                } else {
                    $response = ['data' => '', 'error' => 'Save coupon error.'];
                }
            } else {
                $response = ['data' => '', 'error' => 'Not found post CouponManager::updateCoupon()'];
            }
        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into CouponManager::updateCoupon()'];
        }
        return $response;
    }

    public static function deleteCoupon($id)
    {
        try {

            $deleted = wp_delete_post($id);

            if ($deleted) {
                $response = ['data' => ['successfully'], 'error' => ''];
            } else {
                $response = ['data' => '', 'error' => 'Not remove post CouponManager::deleteCoupon()'];
            }
        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into CouponManager::deleteCoupon()'];
        }
        return $response;
    }

}
