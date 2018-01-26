<?php
class OrderStatuses
{
    const VERIFIED = 'wc-verified';
    const VERIFICATION_REQUIRED = 'wc-verif-rqrd';
    const VERIFICATION_UNSUCCESSFUL = 'wc-verif-fld';
    const PACKING = 'wc-packing';
    const SHIPPED = 'wc-shipped';
    const ON_HOLD_STOCK_UNAVAILABLE = 'wc-on-hold-stockun';
    const RETURN_REQUESTED = 'wc-return-requested';
    const ON_HOLD_CUSTOMER_RETURN = 'wc-on-hold-cust-ret';
    const ORDER_RETURNED_TO_WAREHOUSE = 'wc-ret-to-wrh';
    const PRODUCT_RETURNED_TO_COURIER = 'wc-prdct-ret-to-crr';
    const PROBLEMS_OF_COURIER_SERVICE = 'wc-prblm-of-crr';
    const SUCCESSFUL_RETURN_FROM_CUSTOMER = 'wc-suc-ret-cstmr';
    const AWAITING_DISPATCH = 'wc-pending-dispatch';
    const DELIVERED = 'wc-delivered';
    const CANCELED_UNVERIFIED = 'wc-canceled-unverif';
    const CANCELED_STOCK_UNAVAILABLE = 'wc-canceled-stockun';
    const CANCELED_RETURNED='wc-canceled-returned';
    const DELIVERY_FAILED='wc-delivery-failed';

     /**
     * @param string $code
     * @return string
     */
    static function getFullOrderCode($code){
        $WC_PREFIX='wc-';
        return $WC_PREFIX.$code;
    }
     /**
     * @param string $order_status
     * @return bool
     */
    static function isStatusExist($order_status){
        $this_class=new  ReflectionClass(__CLASS__);
        $existing_statuses = array_flip($this_class->getConstants());
        return array_key_exists($order_status,$existing_statuses);
    }

    /**
     * @param WC_Order $order
     * @return bool
     */
    static function PlaceRelatedAction($order){
        $WC_ORDER_STATUS_TAG='woocommerce_order_status_';

        $status=self::getFullOrderCode($order->get_status());

        if(self::isStatusExist($status)){
            do_action($WC_ORDER_STATUS_TAG.$status, $order);
            return true;
        }
        return false;

    }

}
