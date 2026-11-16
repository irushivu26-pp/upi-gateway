<?php

require_once('../database/config.php');

$upiuid = "paytmqr1jngvmqlwq@paytm";

$token = '1efcd7-83b882-99d4c6-8dac5d-3cb612'; 

$secret = 'feuHlEwzeO'; 

$UPBPAY_ENVIRONMENT = 'PROD';

$UPBPAY_TXN_URL='https://upbpay.tech/order/paytm';
$UPBPAY_STATUS_URL='https://upbpay.tech/order/status_paytm';
if($UPBPAY_ENVIRONMENT == 'PROD') {
$UPBPAY_TXN_URL='https://info.aisensy.in/order/mobile';	
$UPBPAY_TXN_URL='https://info.aisensy.in/order/mobile';	

}
?>