<?php
require_once __DIR__ . '/../config.php';

$gateway_txn = uniqid();

$order_id = $_POST['order_id'] ?? '';
$hash = $_POST['hash'] ?? '';

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
