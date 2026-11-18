<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../pages/dbFunctions.php';

// Accept both POST and GET from Paytm Webhook/Callback
$raw_input = file_get_contents('php://input');
$json_data = json_decode($raw_input, true);

$data = array_merge($_GET, $_POST);
if (is_array($json_data)) {
    $data = array_merge($data, $json_data);
    if (isset($json_data['body']) && is_array($json_data['body'])) {
        $data = array_merge($data, $json_data['body']);
        if (isset($json_data['body']['resultInfo']) && is_array($json_data['body']['resultInfo'])) {
            $data['status'] = $json_data['body']['resultInfo']['resultStatus'] ?? ($data['status'] ?? '');
            $data['respCode'] = $json_data['body']['resultInfo']['resultCode'] ?? ($data['respCode'] ?? '');
            $data['respMsg'] = $json_data['body']['resultInfo']['resultMsg'] ?? ($data['respMsg'] ?? '');
        }
    }
}

// 1. Extract Order Identifier (check all merchant reference & transaction reference keys)
$order_id = trim(
    $data['ORDERID'] ?? (
    $data['orderId'] ?? (
    $data['order_id'] ?? (
    $data['ORDER_ID'] ?? (
    $data['MERC_UNQ_REF'] ?? (
    $data['merUnqRef'] ?? (
    $data['mercUnqRef'] ?? (
    $data['merchantOrderId'] ?? (
    $data['merchantTransactionId'] ?? (
    $data['client_txn_id'] ?? (
    $data['tr'] ?? (
    $data['txnRef'] ?? (
    $data['txId'] ?? ''
))))))))))))
);

// 2. Extract Status
$status_raw = strtoupper(trim($data['STATUS'] ?? ($data['status'] ?? ($data['resultStatus'] ?? ($data['txnStatus'] ?? '')))));
$resp_code = trim($data['RESPCODE'] ?? ($data['respCode'] ?? ''));
$is_success = ($status_raw === 'TXN_SUCCESS' || $status_raw === 'SUCCESS' || $status_raw === 'COMPLETED' || $status_raw === 'PAID' || $resp_code === '01');

// 3. Extract UTR / Transaction IDs
$utr = trim($data['BANKTXNID'] ?? ($data['bankTxnId'] ?? ($data['utr'] ?? ($data['bank_txn_id'] ?? ($data['bankTxnNo'] ?? '')))));
$paytm_txn_id = trim($data['TXNID'] ?? ($data['txnId'] ?? ($data['gateway_txn'] ?? ($data['paytmTxnId'] ?? ''))));
$amount = trim($data['TXNAMOUNT'] ?? ($data['txnAmount'] ?? ($data['amount'] ?? '')));
$mid = trim($data['MID'] ?? ($data['mid'] ?? ''));

$conn = connect_database();
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+05:30'");

// 4. Resolve Order in Database
$matched_order_id = '';
$redirect_url = '';
$user_callback_url = '';
$order_amount = '';
$gateway_txn = '';
$remark1 = '';

if (!empty($order_id)) {
    $clean_oid = mysqli_real_escape_string($conn, $order_id);
    $q = "SELECT o.order_id, o.redirect_url, o.gateway_txn, o.amount, o.remark1, u.callback_url 
          FROM orders o 
          LEFT JOIN users u ON o.user_token = u.user_token 
          WHERE o.order_id = '$clean_oid' 
             OR o.paytm_txn_ref = '$clean_oid' 
             OR o.gateway_txn = '$clean_oid' 
             OR o.byteTransactionId = '$clean_oid'
          LIMIT 1";
    $res = $conn->query($q);
    if ($res && $row = $res->fetch_assoc()) {
        $matched_order_id = $row['order_id'];
        $redirect_url = $row['redirect_url'];
        $gateway_txn = $row['gateway_txn'];
        $order_amount = $row['amount'];
        $remark1 = $row['remark1'];
        $user_callback_url = $row['callback_url'];
    }
}

// Fallback: match by recent pending order with same amount if order_id is missing or Paytm internal order ID
if (empty($matched_order_id) && !empty($amount)) {
    $amt_num = (float)$amount;
    $clean_amt = number_format($amt_num, 2, '.', '');
    $q_amt = "SELECT o.order_id, o.redirect_url, o.gateway_txn, o.amount, o.remark1, u.callback_url 
              FROM orders o 
              LEFT JOIN users u ON o.user_token = u.user_token 
              WHERE o.status = 'PENDING' AND (o.amount = '$clean_amt' OR CAST(o.amount AS DECIMAL(10,2)) = $amt_num) 
              ORDER BY o.id DESC LIMIT 1";
    $res_amt = $conn->query($q_amt);
    if ($res_amt && $row = $res_amt->fetch_assoc()) {
        $matched_order_id = $row['order_id'];
        $redirect_url = $row['redirect_url'];
        $gateway_txn = $row['gateway_txn'];
        $order_amount = $row['amount'];
        $remark1 = $row['remark1'];
        $user_callback_url = $row['callback_url'];
    }
}

// 5. Update Order Status if Payment is Successful
if (!empty($matched_order_id) && $is_success) {
    $utr_val = !empty($utr) ? $utr : (!empty($paytm_txn_id) ? $paytm_txn_id : ('PTM_' . time()));
    $up_clean_oid = mysqli_real_escape_string($conn, $matched_order_id);
    $up_utr = mysqli_real_escape_string($conn, $utr_val);
    $up_txn = !empty($paytm_txn_id) ? mysqli_real_escape_string($conn, $paytm_txn_id) : $gateway_txn;

    $conn->query("UPDATE orders SET status = 'SUCCESS', utr = '$up_utr', gateway_txn = '$up_txn' WHERE order_id = '$up_clean_oid'");

    $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                  VALUES ('$up_txn', 'TXN_SUCCESS', '$up_clean_oid', 'paytm@upi', 'Customer', 'Paytm', '$order_amount', '', 'Paytm Callback', '$up_txn', 1, '$up_utr')
                  ON DUPLICATE KEY UPDATE status='TXN_SUCCESS', UTR='$up_utr'");

    // Update MID in paytm_tokens if Paytm sent a real MID
    if (!empty($mid) && $mid !== 'PAYTM_MID_101') {
        $clean_mid = mysqli_real_escape_string($conn, $mid);
        $conn->query("UPDATE paytm_tokens SET MID = '$clean_mid' WHERE id = 1");
    }

    // 6. Automatically dispatch callback to PayPrints
    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($matched_order_id, 'SUCCESS', $utr_val);
    }
}

$conn->close();

// 7. Return Response (JSON for server-to-server webhook, HTML redirect if browser)
$accept_hdr = $_SERVER['HTTP_ACCEPT'] ?? '';
if (strpos($accept_hdr, 'text/html') !== false && !empty($redirect_url)) {
    $sep = (strpos($redirect_url, '?') !== false) ? '&' : '?';
    $target_redir = $redirect_url . $sep . 'order_id=' . urlencode($matched_order_id) . '&status=SUCCESS&utr=' . urlencode($utr);
    header("Location: $target_redir");
    exit;
}

header('Content-Type: application/json');
echo json_encode([
    'STATUS' => $is_success ? 'TXN_SUCCESS' : 'TXN_FAILURE',
    'RESPCODE' => $is_success ? '01' : '141',
    'RESPMSG' => $is_success ? 'Txn Success' : 'Txn Failed or Pending',
    'ORDERID' => $matched_order_id ?: $order_id,
    'status' => $is_success,
    'order_id' => $matched_order_id ?: $order_id,
    'utr' => $utr,
    'callback_triggered' => !empty($matched_order_id) && $is_success
]);
?>