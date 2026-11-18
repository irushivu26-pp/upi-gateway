<?php
/**
 * PhonePe Payment Status Check API
 * Responds with 'success', 'PENDING', or 'FAILURE' for AJAX polling
 */
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../pages/dbFunctions.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../config.php';

$conn = connect_database();
if (!$conn || $conn->connect_error) {
    ob_clean();
    die("Database connection failed");
}

$raw_id = $_POST['byte_order_status'] ?? ($_POST['order_id'] ?? ($_POST['TransactionId'] ?? ($_GET['byte_order_status'] ?? ($_GET['order_id'] ?? ''))));
$byteTransactionId = trim($raw_id);
$incoming_utr = trim($_POST['utr'] ?? ($_GET['utr'] ?? ''));

if (empty($byteTransactionId)) {
    ob_clean();
    header('HTTP/1.1 400 Bad Request');
    exit('Missing order identifier');
}

$clean_id = mysqli_real_escape_string($conn, $byteTransactionId);

// 1. Locate Order in database (by byteTransactionId or order_id)
$sql = "SELECT * FROM orders WHERE byteTransactionId='$clean_id' OR order_id='$clean_id' LIMIT 1";
$res = $conn->query($sql);
$order = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;

if (!$order) {
    ob_clean();
    die("Byte error");
}

$order_id = $order['order_id'];
$bytehackamount = (float)$order['amount'];
$user_token = $order['user_token'];
$gateway_txn = $order['gateway_txn'];
$current_status = strtoupper(trim($order['status'] ?? 'PENDING'));
$user_id = $order['user_id'] ?? 1;

// 2. If order is ALREADY SUCCESS in database -> ALWAYS return 'success'!
if ($current_status === 'SUCCESS' || $current_status === 'COMPLETED') {
    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 3. If a UTR number was submitted (manual verification / confirmation)
if (!empty($incoming_utr) && strlen($incoming_utr) >= 4) {
    $clean_utr = mysqli_real_escape_string($conn, $incoming_utr);
    $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_utr' WHERE order_id='$order_id'");

    $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                  VALUES ('$clean_utr', 'SUCCESS', '$order_id', 'phonepe@upi', 'Customer', 'PhonePe', '$bytehackamount', '$user_token', 'Manual UTR Verification', '$byteTransactionId', '$user_id', '$clean_utr')
                  ON DUPLICATE KEY UPDATE status='SUCCESS', UTR='$clean_utr'");

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
    }

    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 4. Check PhonePe Transaction Feed
$txn_url = $murl . '/phnpe/user_txn.php?no=' . urlencode($user_token);
$txn_data = @file_get_contents($txn_url);
if (!empty($txn_data) && strlen($txn_data) > 6) {
    $clean_json = substr($txn_data, 6);
    $jsonData = json_decode($clean_json, true);
    $results = $jsonData["data"]["results"] ?? [];

    $paymentState = '';
    $amo = 0;
    $UTR = '';
    $transactionId = '';

    foreach ($results as $result) {
        if (($result['merchantTransactionId'] ?? '') == $byteTransactionId) {
            $amo = isset($result["amount"]) ? ($result["amount"] / 100) : 0;
            $transactionId = $result["transactionId"] ?? '';
            $paymentState = strtoupper(trim($result["payResponseCode"] ?? ''));
            $UTR = $result["utr"] ?? $transactionId;
            break;
        }
    }

    if ($paymentState === 'SUCCESS' && abs($amo - $bytehackamount) < 0.01) {
        $clean_utr = mysqli_real_escape_string($conn, $UTR);
        $clean_txnid = mysqli_real_escape_string($conn, $transactionId);
        $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_utr', gateway_txn='$clean_txnid' WHERE order_id='$order_id'");

        $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                      VALUES ('$clean_txnid', 'SUCCESS', '$order_id', 'phonepe@upi', 'Customer', 'PhonePe', '$bytehackamount', '$user_token', 'PhonePe API Verified', '$byteTransactionId', '$user_id', '$clean_utr')
                      ON DUPLICATE KEY UPDATE status='SUCCESS', UTR='$clean_utr'");

        if (function_exists('notify_merchant_callback')) {
            notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
        }

        ob_clean();
        echo 'success';
        $conn->close();
        exit;
    }
}

// 5. Check Reports table fallback
$rep_res = $conn->query("SELECT status, UTR FROM reports WHERE order_id='$order_id' AND (status='SUCCESS' OR status='TXN_SUCCESS') LIMIT 1");
if ($rep_res && $rep_res->num_rows > 0) {
    $rep_row = $rep_res->fetch_assoc();
    $utr_val = $rep_row['UTR'] ?? ('PHN_' . time());
    $conn->query("UPDATE orders SET status='SUCCESS', utr='$utr_val' WHERE order_id='$order_id'");

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $utr_val);
    }

    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

if ($current_status === 'FAILED' || $current_status === 'FAILURE') {
    ob_clean();
    echo 'FAILURE';
} else {
    ob_clean();
    echo 'PENDING';
}

$conn->close();
exit;
?>
