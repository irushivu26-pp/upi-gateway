<?php
/**
 * HDFC Payment Status Check API
 * Responds with 'success', 'PENDING', or 'FAILURE' for AJAX polling
 */
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../pages/dbFunctions.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../partner/config.php';
require_once __DIR__ . '/../config.php';

$raw_id = $_POST['order_id'] ?? ($_POST['byte_order_status'] ?? ($_GET['order_id'] ?? ''));
$order_id = trim($raw_id);
$incoming_utr = trim($_POST['utr'] ?? ($_GET['utr'] ?? ''));

if (empty($order_id)) {
    ob_clean();
    echo "Order ID is not provided";
    exit;
}

$conn = connect_database();
if (!$conn || $conn->connect_error) {
    ob_clean();
    die("Database connection failed");
}

$clean_id = mysqli_real_escape_string($conn, $order_id);
$sql_order = "SELECT * FROM orders WHERE order_id='$clean_id' OR byteTransactionId='$clean_id' LIMIT 1";
$res_order = $conn->query($sql_order);
$order = ($res_order && $res_order->num_rows > 0) ? $res_order->fetch_assoc() : null;

if (!$order) {
    ob_clean();
    echo "Order not found";
    $conn->close();
    exit;
}

$order_id = $order['order_id'];
$user_token = $order['user_token'];
$hdfc_txn = $order['HDFC_TXNID'] ?? '';
$db_status = strtoupper(trim($order['status'] ?? 'PENDING'));

// 1. If already SUCCESS -> return success
if ($db_status === "SUCCESS" || $db_status === "COMPLETED") {
    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 2. If UTR provided
if (!empty($incoming_utr) && strlen($incoming_utr) >= 4) {
    $clean_utr = mysqli_real_escape_string($conn, $incoming_utr);
    $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_utr' WHERE order_id='$order_id'");

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $clean_utr);
    }

    ob_clean();
    echo 'success';
    $conn->close();
    exit;
}

// 3. Query HDFC transaction parameters if configured
$sql_hdfc = "SELECT * FROM hdfc WHERE user_token='$user_token' LIMIT 1";
$res_hdfc = $conn->query($sql_hdfc);
$hdfc_row = ($res_hdfc && $res_hdfc->num_rows > 0) ? $res_hdfc->fetch_assoc() : null;

if ($hdfc_row && !empty($hdfc_txn)) {
    $seassion_id_hdfc = $hdfc_row['seassion'] ?? '';
    $hdfc_number = $hdfc_row['number'] ?? '';

    $server_host = !empty($server) ? $server : 'localhost';
    $txn_data = @file_get_contents('https://' . $server_host . '/payment/mstatement.php?no=' . $hdfc_number . '&session=' . $seassion_id_hdfc);
    $json_decode = (!empty($txn_data)) ? json_decode($txn_data, true) : null;
    $results = $json_decode["transactionParams"] ?? [];

    foreach ($results as $result) {
        $txnid = $result["txnid"] ?? '';
        $status = $result["status"] ?? '';
        $issuerRefNo = $result["issuerRefNo"] ?? $txnid;

        if ($status == '3' && $txnid == $hdfc_txn) {
            $clean_ref = mysqli_real_escape_string($conn, $issuerRefNo);
            $conn->query("UPDATE orders SET status='SUCCESS', utr='$clean_ref' WHERE order_id='$order_id'");

            if (function_exists('notify_merchant_callback')) {
                notify_merchant_callback($order_id, 'SUCCESS', $clean_ref);
            }

            ob_clean();
            echo 'success';
            $conn->close();
            exit;
        }
    }
}

// 4. Fallback status
$chk = $conn->query("SELECT status FROM orders WHERE order_id='$order_id' LIMIT 1");
$chk_row = ($chk && $chk->num_rows > 0) ? $chk->fetch_assoc() : null;
$final_status = strtoupper(trim($chk_row['status'] ?? 'PENDING'));

if ($final_status === 'SUCCESS' || $final_status === 'COMPLETED') {
    ob_clean();
    echo 'success';
} elseif ($final_status === 'FAILED' || $final_status === 'FAILURE') {
    ob_clean();
    echo 'FAILURE';
} else {
    ob_clean();
    echo 'PENDING';
}

$conn->close();
exit;
?>
