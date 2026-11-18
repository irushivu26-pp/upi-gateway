<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../pages/dbFunctions.php';

$gateway_txn = uniqid();

$order_id = $_REQUEST['order_id'] ?? '';
$hash = $_REQUEST['hash'] ?? '';
$status = $_REQUEST['status'] ?? 'SUCCESS';
$utr = $_REQUEST['utr'] ?? '';

if (!empty($order_id) && function_exists('notify_merchant_callback')) {
    notify_merchant_callback($order_id, $status, $utr);
}

$url = $murl . "/api/order-info";

$data = array(
    "hash" => $hash,
    "order_id" => $order_id,
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json",
        'method'  => 'POST',
        'content' => json_encode($data),
    ),
);

$context  = stream_context_create($options);
echo $response = file_get_contents($url, false, $context);




 $data = json_decode($response,true);
 
 
 
?>
