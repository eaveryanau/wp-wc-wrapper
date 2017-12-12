<?php

/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */
Class SettingsManager
{

    public static function saveSettings($data)
    {
        //currency
        if (get_option('woocommerce_currency') != $data['currency']) {
            if (!update_option('woocommerce_currency', $data['currency'])) {
                return ['data' => '', 'error' => 'Failed update currency'];
            }
        }


        try {
            // payments

//            if($data['payments']){
                //bacs
                if (in_array('bacs', $data['payments'])) {
                    // return ['data' => ['bacs'], 'error' => ''];
                    $dt = get_option('woocommerce_bacs_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_bacs_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_bacs_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_bacs_settings', $dt);
                }

                //cheque
                if (in_array('cheque', $data['payments'])) {
                    $dt = get_option('woocommerce_cheque_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_cheque_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_cheque_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_cheque_settings', $dt);
                }

                //cod
                if (in_array('cod', $data['payments'])) {
                    $dt = get_option('woocommerce_cod_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_cod_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_cod_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_cod_settings', $dt);
                }

                //paypal
                if (in_array('paypal', $data['payments'])) {
                    $dt = get_option('woocommerce_paypal-ec_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_paypal-ec_settings', $data);
                } else {
                    $dt = get_option('woocommerce_paypal-ec_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_paypal-ec_settings', $dt);
                }

                //payfort
                if (in_array('payfort', $data['payments'])) {
                    $dt = get_option('woocommerce_payfort_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_payfort_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_payfort_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_payfort_settings', $dt);
                }

                //sadaq
                if (in_array('payfort_fort_sadad', $data['payments'])) {
                    $dt = get_option('woocommerce_payfort_fort_sadad_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_payfort_fort_sadad_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_payfort_fort_sadad_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_payfort_fort_sadad_settings', $dt);
                }

                //naps
                if (in_array('payfort_fort_qpay', $data['payments'])) {
                    $dt = get_option('woocommerce_payfort_fort_qpay_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_payfort_fort_qpay_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_payfort_fort_qpay_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_payfort_fort_qpay_settings', $dt);
                }
//            }


            //shipping
//            if($data['shipping']){
                //naqel
                if (in_array('wc_naqel_shipping_method', $data['shipping'])) {
                    $dt = get_option('woocommerce_wc_naqel_shipping_method_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_wc_naqel_shipping_method_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_wc_naqel_shipping_method_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_wc_naqel_shipping_method_settings', $dt);
                }

                //fetchr
                if (in_array('wc_fetchr_shipping_method', $data['shipping'])) {
                    $dt = get_option('woocommerce_wc_fetchr_shipping_method_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_wc_fetchr_shipping_method_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_wc_fetchr_shipping_method_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_wc_fetchr_shipping_method_settings', $dt);
                }

                //aramex
                if (in_array('aramex', $data['shipping'])) {
                    $dt = get_option('woocommerce_aramex_settings');
                    $dt['enabled'] = 'yes';
                    update_option('woocommerce_aramex_settings', $dt);
                } else {
                    $dt = get_option('woocommerce_aramex_settings');
                    $dt['enabled'] = 'no';
                    update_option('woocommerce_aramex_settings', $dt);
                }
//            }
//            else{
//
//            }

            $response = ['data' => ['result' => 'successfully'], 'error' => ''];
        } catch (\Exception $e) {
            $response = ['data' => '', 'error' => 'Error with saving payments or shipping methods'];
        }
        return $response;
    }

    public static function getCurrenciesList()
    {
        /*return json_encode([
                'supported_currencies'=>get_woocommerce_currencies(),
                'active_currency'=>get_woocommerce_currency()
            ]);*/

        $response = ['data' => [
            'supported_currencies' => get_woocommerce_currencies(),
            'active_currency' => get_woocommerce_currency()
        ], 'error' => ''];

        return $response;

    }

    public static function getPaymentGateways()
    {
        $gateways = WC()->payment_gateways->payment_gateways();
//        $active_gateways = WC()->payment_gateways
        $result = [];

        foreach ($gateways as $key => $gateway) {

            $result[$key] = [
                'title' => $gateway->get_title(),
                'enabled' => $gateway->enabled,
                'availability' => $gateway->is_available()
            ];
        }

        return ['data' => ['all_gateways' => $result,
        ],
            'error' => ''
        ];
    }

    public static function getShippingMethods()
    {
        $methods = WC()->shipping->get_shipping_methods();
        $result = [];
        foreach ($methods as $key => $method) {
            $result[$key] = [
                'title' => $method->get_method_title(),
                'enabled' => $method->enabled,
            ];
        }
        return ['data' => ['all_methods' => $result], 'error' => ''];
    }

}

