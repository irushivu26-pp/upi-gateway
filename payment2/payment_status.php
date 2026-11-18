<?php
error_reporting(0);
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $byteTransactionId = $_POST['byte_order_status'];
    // continue processing the POST request
} else {
    // Send a 403 Forbidden HTTP status code
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

$sqlSelectOrderscxr = "SELECT * FROM orders WHERE byteTransactionId=?";
$stmtSelectOrderscxr = $conn->prepare($sqlSelectOrderscxr);
$stmtSelectOrderscxr->bind_param("s", $byteTransactionId);
$stmtSelectOrderscxr->execute();
$resultSelectOrders = $stmtSelectOrderscxr->get_result();
$cxrrrowOrders = $resultSelectOrders->fetch_assoc();
$stmtSelectOrderscxr->close();

if (!$cxrrrowOrders) {
    die("Byte error");
}

$order_id = $cxrrrowOrders['order_id'];
$bytehackamount = $cxrrrowOrders['amount'];

$sqlCheckStatus = "SELECT status FROM orders WHERE order_id=?";
$stmtCheckStatus = $conn->prepare($sqlCheckStatus);
$stmtCheckStatus->bind_param("s", $order_id);
$stmtCheckStatus->execute();
$resultCheckStatus = $stmtCheckStatus->get_result();

if ($resultCheckStatus->num_rows > 0) {
    $rowCheckStatus = $resultCheckStatus->fetch_assoc();
    if ($rowCheckStatus['status'] === 'SUCCESS') {
        echo 'Order already processed';
        $stmtCheckStatus->close();
        $conn->close();
        exit;
    }
}

$stmtCheckStatus->close();

$sqlSelectOrders = "SELECT * FROM orders WHERE order_id=?";
$stmtSelectOrders = $conn->prepare($sqlSelectOrders);
$stmtSelectOrders->bind_param("s", $order_id);
$stmtSelectOrders->execute();
$resultSelectOrders = $stmtSelectOrders->get_result();
$rowOrders = $resultSelectOrders->fetch_assoc();
$stmtSelectOrders->close();

if (!$rowOrders) {
    die("Order not found");
}

$user_token = $rowOrders['user_token'];

$sqlSelectUser = "SELECT * FROM users WHERE user_token=?";
$stmtSelectUser = $conn->prepare($sqlSelectUser);
$stmtSelectUser->bind_param("s", $user_token);
$stmtSelectUser->execute();
$resultSelectUser = $stmtSelectUser->get_result();
$rowUser = $resultSelectUser->fetch_assoc();
$stmtSelectUser->close();

$txn_url = $murl . '/phnpe/user_txn.php?no=' . urlencode($user_token);
$txn_data = @file_get_contents($txn_url); 
$txn_data = substr($txn_data, 6);

$obj = json_decode($txn_data);
$data = $obj->data;

$jsonData = json_decode($txn_data, true);
$data = $jsonData["data"];
$results = $data["results"];

foreach ($results as $result) {
    if ($result['merchantTransactionId'] == $byteTransactionId) {
        $cxrmerchantTransactionId = $result["merchantTransactionId"];
        $customerDetails = $result["customerDetails"];
        $user_name = $customerDetails["userName"];
        $paymentApp = $result["paymentApp"];
        $paymentApp = $paymentApp["paymentApp"];
        $logo = $paymentApp["logo"];
        $amount = $result["amount"];
        $amount = $amount / 100;
        $amo = $amount;
        $transactionId = $result["transactionId"];
        $paymentState = $result["payResponseCode"];
        $transactionDate = $result["transactionDate"];
        $transactionNote = $result["transactionNote"];
        $transactionDate = date('m/d/Y', $transactionDate);
        $Status = $result["Status"];
        $UTR = $result["utr"];
        $vpa = $result["vpa"];
    }
}

if ($paymentState == 'SUCCESS' && $amo === $bytehackamount) {
    $sql = "UPDATE orders SET status='SUCCESS' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

    $sql = "UPDATE orders SET utr=? WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $UTR, $order_id);
    $stmt->execute();
    $stmt->close();

    $callback_url = $rowUser['callback_url'] ?? '';
    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $UTR);
    } elseif (!empty($callback_url)) {
        $sep = (strpos($callback_url, '?') !== false) ? '&' : '?';
        $cb_url = $callback_url . $sep . 'order_id=' . urlencode($order_id) . '&status=SUCCESS&utr=' . urlencode($UTR) . '&amount=' . urlencode($amo) . '&gateway_txn=' . urlencode($transactionId);
        @file_get_contents($cb_url);
    }
    
    echo 'success';
} else {
    echo 'PENDING';
}

$conn->close();
?>
