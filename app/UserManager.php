<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class UserManager
{

    public static function getUsers($role = 'customer')
    {

        try {
            $data = [];

            $args = [
                'role__in' => $role
            ];

            $my_user_query = new WP_User_Query($args);
            $customers = $my_user_query->get_results();

            foreach ($customers as $customer) {
                if (in_array('customer', $customer->roles)) {
                    $cc = new WC_Customer($customer->ID);
                    $data [] = [
                        'id' => $cc->get_id(),
                        'username' => $cc->get_username(),
                        'firstname' => $cc->get_first_name(),
                        'lastname' => $cc->get_last_name(),
                        'email' => $cc->get_email(),
                        'date_created' => $cc->get_date_created()->date('Y-m-d'),
                        'display_name' => $cc->get_display_name(),

                        'phone' => $cc->get_billing_phone(),
                        'address' => $cc->get_billing_address(),
                        'order_count' => $cc->get_order_count()
                    ];
                } else {
                    $data [] = [
                        'id' => $customer->ID,
                        'username' => $customer->user_login,
                        'email' => $customer->user_email,
                        'role' => $customer->roles
                    ];
                }

            }
            $response = ['data' => $data, 'error' => ''];
        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into UserManager::getUsers()'];
        }

        return $response;
    }

    public static function getCustomer($id)
    {
        try {
            $customer = new WC_Customer($id);
            if ($customer->get_role() != 'customer') {
                return ['error' => 'customer not found'];
            }
            $data = $customer->get_data();
            $data['date_created'] = $customer->get_date_created()->date('Y-m-d');
            $data['date_modified'] = ($customer->get_date_modified()) ? $customer->get_date_modified()->date('Y-m-d') : null;

            $response = ['data' => $data, 'error' => ''];
        } catch (\Exception $ex) {
            $response = [
                'data' => '',
                'error' => 'Internal error into UserManager::getCustomer()(id=' . $id . ')'
            ];
        }

        return $response;
    }

    public static function updateCustomer($id, $data)
    {


        $customer = new WC_Customer($id);

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

    public static function deleteUser($id)
    {

        if($id == 1){
            $response = ['data' => [], 'error' => 'Superadmin can not delete!'];
        }
        else{
            require_once(ABSPATH . 'wp-admin/includes/user.php');

            $is_customer_delete = wp_delete_user($id);

            $response = ($is_customer_delete) ? ['data' => ['user deleted'], 'error' => ''] : ['data' => [], 'error' => 'not delete'];
        }

        return $response;
    }

    public static function createUsers($data)
    {

        try {
            $user_id = username_exists($data['user_data']['username']);
            if (!$user_id and email_exists($data['user_data']['email']) == false) {
                $user_id = wp_create_user($data['user_data']['username'], $data['user_data']['password'], $data['user_data']['email']);
                if ($data['user_data']['role'] == 'customer') {
                    $customer = new WC_Customer($user_id);
                    $customer->set_billing_address($data['user_data']['address']);
                    $customer->set_billing_phone($data['user_data']['phone']);
                    $customer->set_first_name($data['user_data']['firstname']);
                    $customer->set_last_name($data['user_data']['lastname']);
                    $customer->save();
                } else {
                    $user_id_role = new WP_User($user_id);
                    $user_id_role->set_role($data['user_data']['role']);
                }

                foreach ($data['user_data']['domains'] as $dom) {
                    $flag = false;
                    $blog_id = 0;
                    foreach (get_sites() as $blog) {
                        $blog_id = $blog->blog_id;
                        if ($dom == 'http://' . $blog->domain) {
                            $flag = true;
                            break;
                        }

                    }
                    if ($flag) {
                        add_user_to_blog($blog_id, $user_id, $data['user_data']['role']);
                    } else {
                        remove_user_from_blog($user_id, $blog_id);
                    }
                }

            }

            $data = [];
            $data[] = $user_id;

            $response = ['data' => $data, 'error' => ''];
        } catch (\Exception $ex) {
            $response = ['data' => '', 'error' => 'Internal error into UserManager::createUsers()'];
        }

        return $response;
    }

}