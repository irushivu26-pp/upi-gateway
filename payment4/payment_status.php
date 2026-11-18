<?php
/**
 * BharatPe Payment Status Check API
 * Responds with 'success', 'PENDING', or 'FAILURE' for AJAX polling and JSON for verify.php
 */
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../pages/dbFunctions.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("SET names utf8mb4");
} catch (PDOException $e) {
    ob_clean();
    die("Database connection failed");
}

function bharatpe_trans($pdo, $merchantId, $token, $cookie) {
    $fromDate = date('Y-m-d', strtotime('-2 days'));
    $toDate = date('Y-m-d');

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://payments-tesseract.bharatpe.in/api/v1/merchant/transactions?module=PAYMENT_QR&merchantId=' . $merchantId . '&sDate=' . $fromDate . '&eDate=' . $toDate,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: ' . $token,
            'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $decodedResponse = json_decode($response, true);
    if (is_array($decodedResponse) && isset($decodedResponse['status']) && $decodedResponse['status']) {
        return $decodedResponse['data']['transactions'] ?? [];
    }
    return [];
}

$raw_id = $_POST['byte_order_status'] ?? ($_POST['TransactionId'] ?? ($_POST['order_id'] ?? ($_GET['byte_order_status'] ?? ($_GET['order_id'] ?? ''))));
$transactionId = trim($raw_id);
$utr = trim($_POST['utr'] ?? ($_GET['utr'] ?? ''));
$is_json_requested = isset($_POST['TransactionId']) && isset($_POST['utr']);

if (empty($transactionId)) {
    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['error' => 'Missing transaction identifier']);
    } else {
        echo 'Missing transaction identifier';
    }
    exit;
}

$query = "SELECT * FROM orders WHERE byteTransactionId = :id OR order_id = :id LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $transactionId]);
$row = $stmt->fetch();

if (!$row) {
    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['error' => 'Order not found']);
    } else {
        echo 'Order not found';
    }
    exit;
}

$order_id = $row['order_id'];
$user_token = $row['user_token'];
$redirect_url = !empty($row['redirect_url']) ? $row['redirect_url'] : ($murl . '/callback.php');
$current_status = strtoupper(trim($row['status'] ?? 'PENDING'));

// If already SUCCESS -> return success
if ($current_status === 'SUCCESS' || $current_status === 'COMPLETED') {
    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['status' => 'success', 'redirect_url' => $redirect_url]);
    } else {
        echo 'success';
    }
    exit;
}

// If UTR provided -> verify via BharatPe or accept valid UTR
if (!empty($utr) && strlen($utr) >= 4) {
    $tokenQuery = "SELECT token, cookie, merchantId FROM bharatpe_tokens WHERE user_token = :user_token LIMIT 1";
    $tokenStmt = $pdo->prepare($tokenQuery);
    $tokenStmt->execute(['user_token' => $user_token]);
    $tokenRow = $tokenStmt->fetch();

    $matched = false;
    $matched_ref = $utr;
    if ($tokenRow && !empty($tokenRow['merchantId']) && !empty($tokenRow['token'])) {
        $transactions = bharatpe_trans($pdo, $tokenRow['merchantId'], $tokenRow['token'], $tokenRow['cookie']);
        if (is_array($transactions)) {
            foreach ($transactions as $txn) {
                if (($txn['bankReferenceNo'] ?? '') == $utr) {
                    $matched = true;
                    $matched_ref = $txn['bankReferenceNo'];
                    break;
                }
            }
        }
    }

    // Update order to SUCCESS
    $updateStmt = $pdo->prepare("UPDATE orders SET status = 'SUCCESS', utr = :utr WHERE order_id = :order_id");
    $updateStmt->execute(['order_id' => $order_id, 'utr' => $matched_ref]);

    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($order_id, 'SUCCESS', $matched_ref);
    }

    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['status' => 'success', 'redirect_url' => $redirect_url]);
    } else {
        echo 'success';
    }
    exit;
}

// Fallback status
if ($current_status === 'FAILED' || $current_status === 'FAILURE') {
    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['status' => 'failed', 'redirect_url' => $redirect_url]);
    } else {
        echo 'FAILURE';
    }
} else {
    ob_clean();
    if ($is_json_requested) {
        echo json_encode(['status' => 'pending', 'redirect_url' => $redirect_url]);
    } else {
        echo 'PENDING';
    }
}
exit;
?>
