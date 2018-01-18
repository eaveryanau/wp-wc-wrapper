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

}