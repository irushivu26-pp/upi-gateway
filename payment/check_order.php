<?php
require_once __DIR__ . '/../config.php';
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array('status' => false, 'message' => 'Method Not Allowed'));
    exit;
}

$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);
if (!is_array($data)) {
    $data = array();
}

$user_token = $data['user_token'] ?? ($data['key'] ?? ($_REQUEST['user_token'] ?? ($_REQUEST['key'] ?? '')));
$order_id = $data['order_id'] ?? ($data['client_txn_id'] ?? ($_REQUEST['order_id'] ?? ($_REQUEST['client_txn_id'] ?? '')));

if (empty($user_token) || empty($order_id)) {
    echo json_encode(array(
        'status' => false,
        'message' => 'Missing required parameter: user_token/key or order_id/client_txn_id'
    ));
    exit;
}

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(array('status' => false, 'message' => 'Database connection failed'));
    exit;
}
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+05:30'");

$sql = "SELECT status, amount, utr, create_date, remark1, gateway_txn FROM orders WHERE user_token = ? AND order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_token, $order_id);
$stmt->execute();
$stmt->bind_result($status, $amount, $utr, $create_date, $remark1, $gateway_txn);

if ($stmt->fetch()) {
    $db_status = strtoupper(trim($status ?: 'PENDING'));
    $is_success = ($db_status === 'SUCCESS' || $db_status === 'COMPLETED');
    $is_failed = ($db_status === 'FAILED');

    $status_str = $is_success ? 'COMPLETED' : ($is_failed ? 'FAILED' : 'PENDING');
    $ekqr_status = $is_success ? 'success' : ($is_failed ? 'failure' : 'pending');

    echo json_encode(array(
        'status' => true,
        'paid' => $is_success,
        'txnStatus' => $status_str,
        'message' => $is_success ? 'Transaction Successfully' : ($is_failed ? 'Transaction Failed' : 'Transaction Pending'),
        'resultInfo' => $is_success ? 'Transaction Success' : ($is_failed ? 'Transaction Failed' : 'Transaction Pending'),
        'orderId' => $order_id,
        'client_txn_id' => $order_id,
        'amount' => $amount,
        'date' => $create_date,
        'utr' => $utr ?: '',
        'remark' => $remark1 ?: '',
        'data' => array(
            'status' => $ekqr_status,
            'client_txn_id' => $order_id,
            'order_id' => $order_id,
            'amount' => $amount,
            'utr' => $utr ?: '',
            'upi_txn_id' => $utr ?: $gateway_txn,
            'txn_id' => $gateway_txn ?: $utr,
            'created_at' => $create_date
        )
    ));
    $stmt->close();
    $conn->close();
    exit;
} else {
    echo json_encode(array(
        'status' => false,
        'message' => 'Order not found',
        'data' => null
    ));
    $stmt->close();
    $conn->close();
    exit;
}
?>
