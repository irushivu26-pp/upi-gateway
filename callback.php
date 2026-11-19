<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/pages/dbFunctions.php';

$order_id = trim($_REQUEST['order_id'] ?? '');
$hash = trim($_REQUEST['hash'] ?? '');
$status = trim($_REQUEST['status'] ?? 'SUCCESS');
$utr = trim($_REQUEST['utr'] ?? '');

if (!empty($order_id) && function_exists('notify_merchant_callback')) {
    notify_merchant_callback($order_id, $status, $utr);
}

// Fetch order details directly from DB for 100% speed and reliability
$amount = '';
$create_date = date('d M Y, h:i A');
$merchant_name = 'Merchant';
$redirect_url = '';
$db_status = $status;

if (!empty($order_id) || !empty($hash)) {
    $search_id = !empty($order_id) ? $order_id : $hash;
    $stmt = $conn->prepare("SELECT o.order_id, o.amount, o.utr, o.create_date, o.status, o.redirect_url, u.company, u.name 
                           FROM orders o 
                           LEFT JOIN users u ON o.user_token = u.user_token 
                           WHERE o.order_id = ? OR o.gateway_txn = ? OR o.byteTransactionId = ? 
                           ORDER BY o.id DESC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("sss", $search_id, $search_id, $search_id);
        $stmt->execute();
        $stmt->bind_result($db_oid, $db_amt, $db_utr, $db_date, $db_st, $db_redir, $u_company, $u_name);
        if ($stmt->fetch()) {
            if (empty($order_id)) $order_id = $db_oid;
            $amount = $db_amt;
            if (empty($utr) && !empty($db_utr)) $utr = $db_utr;
            if (!empty($db_date)) $create_date = date('d M Y, h:i A', strtotime($db_date));
            if (!empty($db_st)) $db_status = $db_st;
            $redirect_url = $db_redir;
            $merchant_name = !empty($u_company) ? $u_company : (!empty($u_name) ? $u_name : 'Merchant Store');
        }
        $stmt->close();
    }
}

// Fallback to internal API if DB lookup yielded empty amount
if (empty($amount)) {
    $url = $murl . "/api/order-info";
    $api_data = array("hash" => $hash, "order_id" => $order_id);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($api_data),
            'timeout' => 3
        ),
    );
    $context = @stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    if ($response) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            $amount = $data['amount'] ?? '';
            if (empty($utr)) $utr = $data['utr'] ?? '';
            if (!empty($data['date'])) $create_date = $data['date'];
        }
    }
}

$is_success = (strtoupper($db_status) === 'SUCCESS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $is_success ? 'Payment Successful' : 'Payment Status'; ?> - UPI Gateway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
        }
        .receipt-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        .status-header {
            padding: 30px 20px 20px;
            text-align: center;
            background: <?php echo $is_success ? 'linear-gradient(180deg, #e8f5e9 0%, #ffffff 100%)' : 'linear-gradient(180deg, #fff3e0 0%, #ffffff 100%)'; ?>;
        }
        .checkmark-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: <?php echo $is_success ? '#28a745' : '#ff9800'; ?>;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 34px;
            box-shadow: 0 4px 15px <?php echo $is_success ? 'rgba(40, 167, 69, 0.35)' : 'rgba(255, 152, 0, 0.35)'; ?>;
            margin-bottom: 14px;
            animation: popIn 0.4s ease-out;
        }
        @keyframes popIn {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .amount-display {
            font-size: 34px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 6px;
        }
        .txn-table {
            width: 100%;
            margin: 0;
            font-size: 14px;
        }
        .txn-table td {
            padding: 10px 24px;
            border-bottom: 1px dashed #e9ecef;
        }
        .txn-table tr:last-child td {
            border-bottom: none;
        }
        .txn-label {
            color: #6c757d;
            font-weight: 500;
        }
        .txn-value {
            text-align: right;
            font-weight: 600;
            color: #212529;
            word-break: break-all;
        }
        .action-bar {
            padding: 20px 24px 28px;
            background: #fafbfc;
            border-top: 1px solid #f1f3f5;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-card { box-shadow: none; border: none; max-width: 100%; }
            .action-bar, .auto-redirect { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="status-header">
            <div class="checkmark-circle">
                <i class="fas <?php echo $is_success ? 'fa-check' : 'fa-info'; ?>"></i>
            </div>
            <h4 class="fw-bold mb-1" style="color: <?php echo $is_success ? '#1e7e34' : '#d35400'; ?>;">
                <?php echo $is_success ? 'Payment Successful' : 'Payment Processing'; ?>
            </h4>
            <div class="text-muted small">Paid to <?php echo htmlspecialchars($merchant_name, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php if (!empty($amount)): ?>
                <div class="amount-display">₹<?php echo number_format((float)$amount, 2, '.', ''); ?></div>
            <?php endif; ?>
        </div>

        <table class="txn-table">
            <tbody>
                <?php if (!empty($order_id)): ?>
                <tr>
                    <td class="txn-label">Order ID</td>
                    <td class="txn-value"><?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($utr)): ?>
                <tr>
                    <td class="txn-label">Bank Ref (UTR)</td>
                    <td class="txn-value text-success font-monospace"><?php echo htmlspecialchars($utr, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td class="txn-label">Payment Mode</td>
                    <td class="txn-value"><i class="fas fa-qrcode text-primary me-1"></i> UPI Direct</td>
                </tr>

                <tr>
                    <td class="txn-label">Date & Time</td>
                    <td class="txn-value"><?php echo htmlspecialchars($create_date, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>

                <tr>
                    <td class="txn-label">Status</td>
                    <td class="txn-value">
                        <?php if ($is_success): ?>
                            <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> COMPLETED</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-2 py-1"><?php echo htmlspecialchars($db_status, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="action-bar">
            <?php if (!empty($redirect_url)): ?>
                <div id="countdownNotice" class="auto-redirect text-center small text-muted mb-3">
                    Redirecting back to store in <span id="secs">6</span>s...
                </div>
                <div class="d-grid gap-2">
                    <a href="<?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary fw-semibold py-2">
                        <i class="fas fa-arrow-left me-1"></i> Return to Store
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Receipt
                    </button>
                </div>
                <script>
                    var count = 6;
                    var interval = setInterval(function() {
                        count--;
                        var el = document.getElementById('secs');
                        if (el) el.innerText = count;
                        if (count <= 0) {
                            clearInterval(interval);
                            window.location.href = <?php echo json_encode($redirect_url); ?>;
                        }
                    }, 1000);
                </script>
            <?php else: ?>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary fw-semibold py-2" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Receipt
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
