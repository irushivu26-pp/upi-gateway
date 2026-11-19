<?php
/**
 * Paytm Payment Status Check API
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

// 1. Locate Order in database (by byteTransactionId, order_id, or paytm_txn_ref)
$sql = "SELECT * FROM orders WHERE byteTransactionId='$clean_id' OR order_id='$clean_id' OR paytm_txn_ref='$clean_id' LIMIT 1";
$res = $conn->query($sql);
$order = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;

if (!$order) {
    ob_clean();
    die("Byter error");
}

$order_id = $order['order_id'];
$user_token = $order['user_token'];
$amount = $order['amount'];
$gateway_txn = $order['gateway_txn'];
$paytm_txn_ref = $order['paytm_txn_ref'] ?? '';
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

    // Insert or update reports record
    $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                  VALUES ('$clean_utr', 'TXN_SUCCESS', '$order_id', 'paytm@upi', 'Customer', 'UPI', '$amount', '$user_token', 'Manual UTR Verification', '$paytm_txn_ref', '$user_id', '$clean_utr')
                  ON DUPLICATE KEY UPDATE status='TXN_SUCCESS', UTR='$clean_utr'");

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
    }

    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 4. Fetch Paytm Merchant Credentials
$mid_res = $conn->query("SELECT MID FROM paytm_tokens WHERE user_token='$user_token' OR user_id='$user_id' LIMIT 1");
$mid_row = ($mid_res && $mid_res->num_rows > 0) ? $mid_res->fetch_assoc() : null;
$bytemerchantid = $mid_row['MID'] ?? '';

// 5. Query Paytm Gateway API if a valid Merchant ID and transaction reference exist
if (!empty($bytemerchantid) && $bytemerchantid !== 'PAYTM_MID_101' && !empty($paytm_txn_ref)) {
    $JsonData = json_encode(array("MID" => $bytemerchantid, "ORDERID" => $paytm_txn_ref));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://securegw.paytm.in/order/status?JsonData=" . urlencode($JsonData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseArray = (!empty($response)) ? json_decode($response, true) : null;

    if (is_array($responseArray) && isset($responseArray['STATUS']) && $responseArray['STATUS'] === 'TXN_SUCCESS') {
        $transactionId = $responseArray['TXNID'] ?? ('PTM_' . time());
        $UTR = $responseArray['BANKTXNID'] ?? $transactionId;

        $clean_utr = mysqli_real_escape_string($conn, $UTR);
        $clean_txnid = mysqli_real_escape_string($conn, $transactionId);
        $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_utr', gateway_txn='$clean_txnid' WHERE order_id='$order_id'");

        $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                      VALUES ('$clean_txnid', 'TXN_SUCCESS', '$order_id', 'paytm@upi', 'Customer', 'Paytm', '$amount', '$user_token', 'Paytm API Verified', '$paytm_txn_ref', '$user_id', '$clean_utr')
                      ON DUPLICATE KEY UPDATE status='TXN_SUCCESS', UTR='$clean_utr'");

        if (function_exists('notify_merchant_callback')) {
            notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
        }

        ob_clean();
        echo 'success';
        $conn->close();
        exit;
    }
}

// 6. Check Reports table fallback (if webhook already registered payment)
$rep_res = $conn->query("SELECT status, UTR FROM reports WHERE order_id='$order_id' AND (status='TXN_SUCCESS' OR status='SUCCESS') LIMIT 1");
if ($rep_res && $rep_res->num_rows > 0) {
    $rep_row = $rep_res->fetch_assoc();
    $utr_val = $rep_row['UTR'] ?? ('PTM_' . time());
    $conn->query("UPDATE orders SET status='SUCCESS', utr='$utr_val' WHERE order_id='$order_id'");

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $utr_val);
    }

    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 6b. Check recent reports by exact amount in the last 5 minutes (webhook auto-match)
if ($current_status === 'PENDING' && !empty($amount)) {
    $clean_amt = mysqli_real_escape_string($conn, $amount);
    $amt_flt = (float)$amount;
    $rec_match = $conn->query("SELECT UTR, transactionId FROM reports 
                               WHERE (amount='$clean_amt' OR CAST(amount AS DECIMAL(10,2))=$amt_flt) 
                                 AND (status='TXN_SUCCESS' OR status='SUCCESS') 
                                 AND date >= (NOW() - INTERVAL 5 MINUTE) 
                               ORDER BY id DESC LIMIT 1");
    if ($rec_match && $rec_match->num_rows > 0) {
        $m_row = $rec_match->fetch_assoc();
        $utr_val = !empty($m_row['UTR']) ? $m_row['UTR'] : $m_row['transactionId'];
        $clean_utr = mysqli_real_escape_string($conn, $utr_val);
        $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_utr' WHERE order_id='$order_id'");
        if (function_exists('notify_merchant_callback')) {
            notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
        }
        ob_clean();
        echo 'success';
        $conn->close();
        exit;
    }
}

// 7. Handle Failure or Pending
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
