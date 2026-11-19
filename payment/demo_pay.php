<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../pages/dbInfo.php';
require_once __DIR__ . '/../pages/dbFunctions.php';

$order_id = $_REQUEST['order_id'] ?? '';
$action = $_REQUEST['action'] ?? '';

if (empty($order_id)) {
    die("<h3>Error: Invalid or missing Order ID</h3>");
}

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("<h3>Error: Database connection failed</h3>");
}
$conn->set_charset('utf8mb4');

$clean_oid = $conn->real_escape_string($order_id);

// Handle Payment Simulation Action
if ($action === 'simulate') {
    $mock_utr = 'DEMO' . time() . rand(1000, 9999);
    $up_sql = "UPDATE orders SET status = 'SUCCESS', utr = '$mock_utr' WHERE order_id = '$clean_oid'";
    $conn->query($up_sql);

    // Fetch order redirect URL
    $sel_sql = "SELECT redirect_url FROM orders WHERE order_id = '$clean_oid' LIMIT 1";
    $sel_res = $conn->query($sel_sql);
    $redir = '';
    if ($sel_res && $row = $sel_res->fetch_assoc()) {
        $redir = $row['redirect_url'] ?? '';
    }

    // Trigger webhook callback
    if (function_exists('notify_merchant_callback')) {
        notify_merchant_callback($clean_oid, 'SUCCESS', $mock_utr);
    }

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => true,
            'message' => 'Payment simulated successfully!',
            'utr' => $mock_utr,
            'order_id' => $clean_oid,
            'redirect_url' => $redir
        ]);
        exit;
    } else {
        if (!empty($redir)) {
            $sep = (strpos($redir, '?') !== false) ? '&' : '?';
            header("Location: " . $redir . $sep . "order_id=" . urlencode($clean_oid) . "&status=SUCCESS&utr=" . urlencode($mock_utr));
        } else {
            echo "<h2>Payment Verified Successfully! (UTR: $mock_utr)</h2><p>You can return to the merchant application.</p>";
        }
        exit;
    }
}

// Fetch order details for display
$sql = "SELECT * FROM orders WHERE order_id = '$clean_oid' LIMIT 1";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    die("<h3>Error: Order not found ($clean_oid)</h3>");
}
$order = $res->fetch_assoc();
$amount = number_format((float)($order['amount'] ?? 1.0), 2, '.', '');
$status = strtoupper(trim($order['status'] ?? 'PENDING'));
$redirect_url = $order['redirect_url'] ?? '';

$demo_upi = "upi://pay?pa=payprints@upi&pn=" . urlencode("PayPrints Demo") . "&am=$amount&cu=INR&tr=" . urlencode($clean_oid) . "&tn=" . urlencode("PayPrints-$clean_oid");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPI Payment Gateway - Demo Mode</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    body { background:#f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:16px; }
    .card { background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; padding:24px 20px; width:100%; max-width:360px; text-align:center; box-shadow:0 10px 25px -5px rgba(0,0,0,0.06); }
    .badge { display:inline-flex; align-items:center; gap:6px; background:#eff6ff; color:#2563eb; font-weight:700; font-size:11px; padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; }
    .title { font-size:16px; font-weight:800; color:#0f172a; margin-bottom:4px; }
    .amt-label { font-size:12px; color:#64748b; font-weight:600; }
    .amt { font-size:28px; font-weight:800; color:#16a34a; margin:4px 0 12px; }
    .apps { display:flex; justify-content:center; gap:6px; margin-bottom:14px; }
    .app-tag { font-size:10px; font-weight:700; padding:3px 8px; border-radius:6px; background:#f1f5f9; color:#475569; }
    .qr-box { background:#ffffff; padding:12px; border-radius:12px; display:inline-block; border:1px solid #cbd5e1; margin-bottom:16px; }
    .btn-simulate { width:100%; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#ffffff; font-weight:700; font-size:14px; padding:12px 14px; border:none; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 12px rgba(16,185,129,0.3); transition:all 0.2s; }
    .btn-simulate:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(16,185,129,0.4); }
    .btn-simulate:active { transform:scale(0.98); }
    .footer-note { font-size:11px; color:#94a3b8; margin-top:10px; }
    .success-card { display:none; padding:20px 10px; }
  </style>
</head>
<body>
  <div class="card" id="mainCard">
    <div class="badge">⚡ Payment Gateway Sandbox</div>
    <div class="title">UPI Payment Demo Gateway</div>
    <div class="amt-label">Order #<?php echo htmlspecialchars($clean_oid); ?></div>
    <div class="amt">₹<?php echo htmlspecialchars($amount); ?></div>
    <div class="apps">
      <span class="app-tag">GPay</span>
      <span class="app-tag">PhonePe</span>
      <span class="app-tag">Paytm</span>
      <span class="app-tag">BHIM</span>
    </div>
    <div class="qr-box">
      <div id="qrcode"></div>
    </div>
    <button class="btn-simulate" id="btnSimulate" onclick="simulatePayment()">
      <span>⚡</span> Simulate Successful Payment
    </button>
    <div class="footer-note">Click button above to simulate instant UPI bank confirmation</div>
  </div>

  <div class="card success-card" id="successCard">
    <div style="font-size:46px; margin-bottom:8px;">✅</div>
    <div class="title" style="color:#059669;">Payment Verified!</div>
    <p style="font-size:13px; color:#64748b; margin-top:6px;">Webhook notified • Releasing print job to printer hardware...</p>
  </div>

  <script>
    const upiUri = "<?php echo $demo_upi; ?>";
    try {
      new QRCode(document.getElementById("qrcode"), {
        text: upiUri,
        width: 145,
        height: 145,
        correctLevel: QRCode.CorrectLevel.M
      });
    } catch(e) {}

    async function simulatePayment() {
      const btn = document.getElementById("btnSimulate");
      btn.disabled = true;
      btn.innerHTML = "<span>⏳</span> Confirming...";

      try {
        const formData = new URLSearchParams();
        formData.append("order_id", "<?php echo $clean_oid; ?>");
        formData.append("action", "simulate");

        const res = await fetch("demo_pay.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
          },
          body: formData.toString()
        });

        const data = await res.json();
        if (data.status) {
          document.getElementById("mainCard").style.display = "none";
          document.getElementById("successCard").style.display = "block";
          if (window.parent) {
            window.parent.postMessage({ type: "PAYMENT_SUCCESS", order_id: "<?php echo $clean_oid; ?>" }, "*");
          }
          if (data.redirect_url) {
            setTimeout(() => {
              window.location.href = data.redirect_url;
            }, 1500);
          }
        } else {
          alert("Simulation failed: " + (data.message || "Unknown error"));
          btn.disabled = false;
          btn.innerHTML = "<span>⚡</span> Simulate Successful Payment";
        }
      } catch (err) {
        alert("Network error simulating payment.");
        btn.disabled = false;
        btn.innerHTML = "<span>⚡</span> Simulate Successful Payment";
      }
    }
  </script>
</body>
</html>
