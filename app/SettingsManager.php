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
        foreach ($data as $key => $value) {
            $wc_key = 'woocommerce_' . $key;
            $result = update_option($wc_key, $value);
            return ['data' => ['result' => $result], 'error' => ''];
        }
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
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
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

