<?php
require_once __DIR__ . '/../config.php';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $murl . '/order2/payment-status',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'byte_order_status: UPB85241709040253',
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
