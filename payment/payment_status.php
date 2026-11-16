<?php
error_reporting(0);
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";
include "../partner/config.php";

// Check if order_id is set in POST data
if (!isset($_POST['order_id'])) {
    echo "Order ID is not provided";
    exit;
}

$order_id = $_POST['order_id'];

// Fetch order details from the database
$sql_order = "SELECT * FROM orders WHERE order_id='$order_id'";
$order_result = getXbyY($sql_order);

// Check if order exists
if (empty($order_result)) {
    echo "Order not found";
    exit;
}

// Extract relevant order details
$user_token = $order_result[0]['user_token'];
$db_description = $order_result[0]['description'];
echo $hdfc_txn = $order_result[0]['HDFC_TXNID'];
$db_byte_status = $order_result[0]['status'];

// Check if order is already processed
if ($db_byte_status == "SUCCESS") {
    echo "Order already processed";
    exit;
}

// Fetch HDFC details from the database
$sql_hdfc = "SELECT * FROM hdfc WHERE user_token='$user_token'";
$hdfc_result = getXbyY($sql_hdfc);

// Check if HDFC details exist
if (empty($hdfc_result)) {
    echo "HDFC details not found";
    exit;
}

$seassion_id_hdfc = $hdfc_result[0]['seassion'];
$hdfc_number = $hdfc_result[0]['number'];

// Fetch user details from the database
$sql_user = "SELECT * FROM users WHERE user_token='$user_token'";
$user_result = getXbyY($sql_user);

// Check if user details exist
if (empty($user_result)) {
    echo "User details not found";
    exit;
}

$callback_url = $user_result[0]['callback_url'];
$mobile = $user_result[0]['mobile'];
$name = $user_result[0]['name'];
$megabyteuserid = $user_result[0]['id'];

// Retrieve transaction data from the server
$txn_data = file_get_contents('https://' . $server . '/payment/mstatement.php?no=' . $hdfc_number . '&session=' . $seassion_id_hdfc);

// Check if transaction data is obtained successfully
if ($txn_data === FALSE) {
    echo "Failed to fetch transaction data";
    exit;
}

// Decode the JSON response
$json_decode = json_decode($txn_data, true);

// Check if JSON decoding was successful
if ($json_decode === null) {
    echo "Failed to decode JSON data";
    exit;
}

$results = $json_decode["transactionParams"];

// Check if order status needs to be updated based on transaction parameters
foreach ($results as $result) {
    $txnid = $result["txnid"];
    $status = $result["status"];
    $paymentType = $result['paymentType'];
    $issuerRefNo = $result["issuerRefNo"];
    $description = $result["description"];

    if ($status == '3' && $txnid == $hdfc_txn) {
        $sq = "UPDATE `orders` SET status='SUCCESS', utr='$issuerRefNo' WHERE order_id='$order_id'";
        setXbyY($sq);

        if (!empty($callback_url)) {
            $sep = (strpos($callback_url, '?') !== false) ? '&' : '?';
            $cb_url = $callback_url . $sep . 'order_id=' . urlencode($order_id) . '&status=SUCCESS&utr=' . urlencode($issuerRefNo) . '&gateway_txn=' . urlencode($txnid);
            @file_get_contents($cb_url);
        }
        break; // Assuming only one successful transaction is needed to update the order status
    }
}

// Fetch the updated order status from the database
$sql_order_status = "SELECT * FROM orders WHERE order_id='$order_id'";
$order_status_result = getXbyY($sql_order_status);
$db_status = $order_status_result[0]['status'];

// Echo the appropriate response based on the updated order status
if ($db_status == "SUCCESS") {
    echo 'success';
} else {
    echo 'PENDING';
}
?>
