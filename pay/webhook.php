<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../pages/dbFunctions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    $data = array_merge($_GET, $_POST);
}

// Support raw SMS / Notification payload from forwarder apps
$raw_text = $data['notification'] ?? ($data['text'] ?? ($data['body'] ?? ($data['msg'] ?? '')));
if (!empty($raw_text)) {
    // Extract SP- or PL- order code if present
    if (preg_match('/(SP-[A-Z0-9_-]+|PL\d+)/i', $raw_text, $m_oid)) {
        $data['order_id'] = $m_oid[1];
    }
    // Extract UTR if present (explicit or 12-digit)
    if (preg_match('/(?:utr|rrn|ref|upi ref|txn id|ref no|transaction id)[\s\:\.\-]+(\d{12})/i', $raw_text, $m_utr_lbl)) {
        $data['utr'] = $m_utr_lbl[1];
    } elseif (preg_match('/\b\d{12}\b/', $raw_text, $m_utr)) {
        $data['utr'] = $m_utr[0];
    }
    // Extract Amount (supports Rs, INR, ₹, and credited amounts)
    if (preg_match('/(?:rs\.?|inr|₹)\s*([\d]+(?:\.\d{1,2})?)/i', $raw_text, $m_amt)) {
        $data['amount'] = $m_amt[1];
    } elseif (preg_match('/(?:credited|received|payment of)\s*(?:by|with)?\s*(?:rs\.?|inr|₹)?\s*([\d]+(?:\.\d{1,2})?)/i', $raw_text, $m_amt2)) {
        $data['amount'] = $m_amt2[1];
    }
}

$status = strtoupper(trim($data['status'] ?? ($data['STATUS'] ?? 'SUCCESS')));
$order_id = trim($data['order_id'] ?? ($data['orderId'] ?? ($data['client_txn_id'] ?? ($data['ORDERID'] ?? ''))));
$utr = trim($data['utr'] ?? ($data['UTR'] ?? ($data['bank_txn_id'] ?? ($data['BANKTXNID'] ?? ''))));
$amount = trim($data['amount'] ?? ($data['TXNAMOUNT'] ?? ''));

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+05:30'");

$matched_oid = '';
if (!empty($order_id)) {
    $clean_oid = mysqli_real_escape_string($conn, $order_id);
    $res_id = $conn->query("SELECT order_id FROM orders WHERE order_id = '$clean_oid' OR paytm_txn_ref = '$clean_oid' OR byteTransactionId = '$clean_oid' OR gateway_txn = '$clean_oid' LIMIT 1");
    if ($res_id && $row = $res_id->fetch_assoc()) {
        $matched_oid = $row['order_id'];
    }
}

// Auto-match order by amount if order_id was not explicitly matched
if (empty($matched_oid) && !empty($amount)) {
    $amt_num = (float)$amount;
    $amt_clean = number_format($amt_num, 2, '.', '');
    $q_match = "SELECT order_id FROM orders WHERE status = 'PENDING' AND (amount = '$amt_clean' OR CAST(amount AS DECIMAL(10,2)) = $amt_num) ORDER BY id DESC LIMIT 1";
    $res_match = $conn->query($q_match);
    if ($res_match && $row = $res_match->fetch_assoc()) {
        $matched_oid = $row['order_id'];
    }
}

if (!empty($matched_oid)) {
    $order_id = $matched_oid;
}

if (empty($order_id)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Missing order_id or matching pending order']);
    $conn->close();
    exit;
}

$db_status = ($status === 'SUCCESS' || $status === 'TXN_SUCCESS' || $status === 'PAID' || $status === 'COMPLETED') ? 'SUCCESS' : 'FAILED';
$utr_val = !empty($utr) ? $utr : ('WB_' . time());

$stmt = $conn->prepare("UPDATE orders SET status = ?, utr = ? WHERE order_id = ?");
$stmt->bind_param("sss", $db_status, $utr_val, $order_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($db_status === 'SUCCESS') {
    $conn->query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, UTR)
                  VALUES ('$utr_val', 'SUCCESS', '$order_id', 'webhook@upi', 'Customer', 'UPI', 0, '', 'Notification Webhook', '$utr_val', 1, '$utr_val')
                  ON DUPLICATE KEY UPDATE status='SUCCESS', UTR='$utr_val'");
}
$conn->close();

// Trigger Merchant Callback automatically (updates PayPrints & releases prints)
if ($db_status === 'SUCCESS' && function_exists('notify_merchant_callback')) {
    notify_merchant_callback($order_id, 'SUCCESS', $utr_val);
}

echo json_encode([
    'status' => true,
    'message' => 'Webhook received and callback triggered automatically',
    'order_id' => $order_id,
    'updated_status' => $db_status,
    'utr' => $utr_val,
    'affected_rows' => $affected
]);
?>