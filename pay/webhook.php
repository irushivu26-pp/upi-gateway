<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    $data = $_POST;
}

$status = strtoupper(trim($data['status'] ?? ($data['STATUS'] ?? 'SUCCESS')));
$order_id = trim($data['order_id'] ?? ($data['orderId'] ?? ($data['client_txn_id'] ?? ($data['ORDERID'] ?? ''))));
$utr = trim($data['utr'] ?? ($data['UTR'] ?? ($data['bank_txn_id'] ?? ($data['BANKTXNID'] ?? ''))));
$amount = trim($data['amount'] ?? ($data['TXNAMOUNT'] ?? ''));

if (empty($order_id)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Missing order_id']);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+05:30'");

$db_status = ($status === 'SUCCESS' || $status === 'TXN_SUCCESS' || $status === 'PAID' || $status === 'COMPLETED') ? 'SUCCESS' : 'FAILED';
$utr_val = !empty($utr) ? $utr : ('WB_' . time());

$stmt = $conn->prepare("UPDATE orders SET status = ?, utr = ? WHERE order_id = ?");
$stmt->bind_param("sss", $db_status, $utr_val, $order_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$conn->close();

echo json_encode([
    'status' => true,
    'message' => 'Webhook processed successfully',
    'order_id' => $order_id,
    'updated_status' => $db_status,
    'affected_rows' => $affected
]);
?>