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

        if(!update_option('woocommerce_currency', $data['currency'])){
            return ['data' => '', 'error' => 'Failed update currency'];
        }

        // payments

        //bacs
        if(in_array('bacs', $data['payments'])){
            $data = unserialize(get_option('woocommerce_bacs_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_bacs_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_bacs_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_bacs_settings',serialize($data));
        }

        //cheque
        if(in_array('cheque', $data['payments'])){
            $data = unserialize(get_option('woocommerce_cheque_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_cheque_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_cheque_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_cheque_settings',serialize($data));
        }

        //cod
        if(in_array('cod', $data['payments'])){
            $data = unserialize(get_option('woocommerce_cod_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_cod_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_cod_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_cod_settings',serialize($data));
        }

        //paypal
        if(in_array('paypal', $data['payments'])){
            $data = unserialize(get_option('woocommerce_paypal-ec_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_paypal-ec_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_paypal-ec_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_paypal-ec_settings',serialize($data));
        }

        //payfort
        if(in_array('payfort', $data['payments'])){
            $data = unserialize(get_option('woocommerce_payfort_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_payfort_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_payfort_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_payfort_settings',serialize($data));
        }

        //sadaq
        if(in_array('payfort_fort_sadad', $data['payments'])){
            $data = unserialize(get_option('woocommerce_payfort_fort_sadad_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_payfort_fort_sadad_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_payfort_fort_sadad_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_payfort_fort_sadad_settings',serialize($data));
        }

        //naps
        if(in_array('payfort_fort_qpay', $data['payments'])){
            $data = unserialize(get_option('woocommerce_payfort_fort_qpay_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_payfort_fort_qpay_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_payfort_fort_qpay_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_payfort_fort_qpay_settings',serialize($data));
        }

        //shipping
        //naqel
        if(in_array('wc_naqel_shipping_method', $data['shipping'])){
            $data = unserialize(get_option('woocommerce_wc_naqel_shipping_method_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_wc_naqel_shipping_method_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_wc_naqel_shipping_method_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_wc_naqel_shipping_method_settings',serialize($data));
        }

        //fetchr
        if(in_array('wc_fetchr_shipping_method', $data['shipping'])){
            $data = unserialize(get_option('woocommerce_wc_fetchr_shipping_method_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_wc_fetchr_shipping_method_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_wc_fetchr_shipping_method_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_wc_fetchr_shipping_method_settings',serialize($data));
        }

        //aramex
        if(in_array('aramex', $data['shipping'])){
            $data = unserialize(get_option('woocommerce_aramex_settings'));
            $data['enabled'] = 'yes';
            update_option('woocommerce_aramex_settings',serialize($data));
        }
        else{
            $data = unserialize(get_option('woocommerce_aramex_settings'));
            $data['enabled'] = 'no';
            update_option('woocommerce_aramex_settings',serialize($data));
        }
        return ['data' => ['result' => 'successfully'], 'error' => ''];
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

