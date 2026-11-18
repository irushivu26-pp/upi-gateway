<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://cdn.zeebiz.com/sites/default/files/2024/01/03/274966-upigpay.jpgg" type="image/*" />
    <meta name="author" content="UPB GROUP">
    <!-- Include SweetAlert2 and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <title>Fastest & Secured UPI Gateway || UPB GROUP</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css">

    <style type="text/css">
      body{
        font-family: 'Poppins', sans-serif;
        background-color: #f6f7fb;
        display: block;
        width: 650px;
        padding: 0 13px;
      }
      a{
        text-decoration: none;
      }
      span {
        font-size: 15px;
      }
      p {
        font-size: 17px;
        line-height: 1.7;
        letter-spacing: 0.7px;
        margin-top: 0;
      }
      .text-center{
        text-align: center
      }
      @media only screen and (max-width: 767px){
        body{
            width: auto;
            margin: 20px auto;
        }
        .logo-sec{
          width: 500px !important;
        }
      }
      @media only screen and (max-width: 575px){
        .logo-sec{
          width: 400px !important;
        }
      }
      @media only screen and (max-width: 480px){
        .logo-sec{
          width: 300px !important;
        }
      }
      @media only screen and (max-width: 360px){
        .logo-sec{
          width: 250px !important;
        }
      }
    </style>
  </head>
<?php
date_default_timezone_set("Asia/Kolkata");
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$link_token = $_GET["token"] ?? '';

// Fetch order_id based on the token from the payment_links table
$sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '$link_token'";
$result = getXbyY($sql_fetch_order_id);

if (count($result) === 0) {
    echo "<div class='text-center mt-5'><h3>Token not found or expired</h3></div>";
    exit;
}

$order_id = $result[0]['order_id'];
$created_raw = $result[0]['created_at'] ?? '';
$created_at = !empty($created_raw) ? strtotime($created_raw) : time();
$current_time = time();
$diff = abs($current_time - $created_at);

if ($diff >= 19500 && $diff <= 20100) {
    $diff = abs($diff - 19800);
}

if ($diff > (20 * 60)) {
    echo "<div class='text-center mt-5'><h3>Payment session has expired. Please create a new link.</h3></div>";
    exit;
}

$slq_p = "SELECT * FROM orders where order_id='$order_id'";
$res_p = getXbyY($slq_p);
if (empty($res_p)) {
    echo "<div class='text-center mt-5'><h3>Order not found</h3></div>";
    exit;
}

$amount = $res_p[0]['amount'];
$hash = $res_p[0]['gateway_txn'];
$user_token = $res_p[0]['user_token'];
$redirect_url = $res_p[0]['redirect_url'];
$cxrkalwaremark = $res_p[0]['byteTransactionId'];  // remark

if (empty($redirect_url)) {
    $redirect_url = $murl . '/callback.php';
}
$server_url = !empty($redirect_url) ? $redirect_url : ($murl . '/');

$slq_p = "SELECT * FROM store_id where user_token='$user_token'";
$res_p = getXbyY($slq_p);    
$unitId = $res_p[0]['unitId'] ?? '';

$slq_p = "SELECT * FROM users where user_token='$user_token'";
$res_p = getXbyY($slq_p);    
$upi_id = $res_p[0]['upi_id'] ?? '';
if (empty($unitId)) {
    $unitId = !empty($res_p[0]['company']) ? $res_p[0]['company'] : (!empty($res_p[0]['name']) ? $res_p[0]['name'] : 'Merchant');
}

$cxrmerchantTransactionId = $cxrkalwaremark;
$asdasd23 = "ARC" . rand(111, 999) . time() . rand(1, 100);
$orders = "upi://pay?pa=$upi_id&am=$amount&pn=" . urlencode($unitId) . "&tn=$asdasd23&tr=$cxrmerchantTransactionId";
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($orders);

$image_content = @file_get_contents($qr_code_url);
$base64_image = $image_content ? base64_encode($image_content) : '';
?>
<body style="margin: 30px auto;">
    <table style="width: 100%">
      <tbody>
        <tr>
          <td>
            <table style="background-color: #f6f7fb; width: 100%">
              <tbody>
                <tr>
                  <td>
                    <table style="margin: 0 auto; background-color: #fff; border-radius: 8px">
                      <tbody>
                        <tr>
                          <td style="padding: 30px"> 
                            <div class="col-md-12">
                              <div class="text-dark"><b><?php echo htmlspecialchars($unitId, ENT_QUOTES, 'UTF-8'); ?></b></div>
                              <div class="text-dark">Amount: <b>₹<?php echo htmlspecialchars($amount, ENT_QUOTES, 'UTF-8'); ?></b></div>
                            </div> 
                            <div class="col-md-12 text-center">
                              <hr>
                              <div class="text-dark"><b>Scan QR code using BHIM or your preferred UPI app</b></div>

                              <div class="col-md-12 text-center mt-1">
                                <div class="text-center">
                                  <img src="/assets/img/gpay.png" alt="gpay" height="20px"> 
                                  <img src="/assets/img/paytm.png" alt="paytm" height="20px">
                                  <img src="/assets/img/phonepe.png" alt="phonepe" height="20px">
                                  <img src="/assets/img/bhim_logo.png" alt="bhim" height="20px">
                                  <img src="/assets/img/amazonpay.png" alt="amazonpay" height="20px">
                                </div>
                                <div class="col-md-12 text-center mt-2">
                                  <?php if (!empty($base64_image)): ?>
                                    <img class="mt-2" src="data:image/png;base64,<?php echo $base64_image; ?>" alt="QR Code" style="max-width: 220px; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                                  <?php else: ?>
                                    <div id="qrcode" class="mt-2 d-flex justify-content-center"></div>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                                    <script>
                                      new QRCode(document.getElementById("qrcode"), {
                                          text: "<?php echo $orders; ?>",
                                          width: 200,
                                          height: 200
                                      });
                                    </script>
                                  <?php endif; ?>
                                  <div class="mt-2">
                                    <a href="<?php echo htmlspecialchars($orders, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary d-md-none"><i class="fas fa-external-link-alt"></i> Open in UPI App</a>
                                  </div>
                                </div>

                                <div class="text-center font-weight-bold mt-2">
                                  This QR code will expire in <b><span id="timeout"></span></b>
                                </div>

                                <!-- Instant UTR Verification for Customer -->
                                <div class="mt-3">
                                  <button type="button" class="btn btn-sm btn-success" onclick="verifyUtrPrompt()"><i class="fas fa-check-circle"></i> Already Paid? Click here to Verify UTR</button>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>

<form id="formSubmit" action="<?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?>" method="post" style="display:none;">
    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="hash" value="<?php echo htmlspecialchars($hash, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="status" value="SUCCESS">
    <input type="hidden" id="formUtr" name="utr" value="">
</form>

<script>
var isSuccessProcessed = false;
var serverUrl = <?php echo json_encode($server_url); ?>;

function upiCountdown(elm, minute, second) {
    var el = document.getElementById(elm);
    if (!el) return;
    el.innerHTML = minute + ":" + (second < 10 ? "0" + second : second);
    startTimer();

    function startTimer() {
        if (isSuccessProcessed) return;
        var presentTime = el.innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = parseInt(timeArray[0], 10);
        var s = parseInt(timeArray[1], 10) - 1;
        if (s < 0) {
            s = 59;
            m = m - 1;
        }
        if (m < 0) {
            Swal.fire({
              title: "Oops",
              text: "Transaction Timeout!",
              icon: "error",
            });
            window.location.href = serverUrl;
            return;
        }
        el.innerHTML = m + ":" + (s < 10 ? "0" + s : s);
        setTimeout(startTimer, 1000);
    }
}

upiCountdown("timeout", 5, 0);

function handleSuccess(utr) {
    if (isSuccessProcessed) return;
    isSuccessProcessed = true;
    if (utr) {
        document.getElementById("formUtr").value = utr;
    }
    Swal.fire({
        title: "Payment Received!",
        text: "Your Payment Received Successfully",
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
    setTimeout(function(){ 
        document.getElementById("formSubmit").submit();
    }, 1800);
}

function check() {
    if (isSuccessProcessed) return;
    $.ajax({
        type: "post",
        url: "/order2/payment-status",
        data: { byte_order_status: <?php echo json_encode($cxrmerchantTransactionId); ?> },
        success: function(data) {
            var res = (data || "").toString().trim().toLowerCase();
            if (res === "success" || res.indexOf("success") !== -1) {
                handleSuccess();
            } else if (res === "failure" || res === "failed") {
                Swal.fire({
                    title: "Failed",
                    text: "Your Payment Was Failed",
                    icon: "error",
                });
                window.location.href = serverUrl;
            }
        }
    });
}

setInterval(check, 3000);
check();

function verifyUtrPrompt() {
    Swal.fire({
        title: 'Verify Payment',
        text: 'Enter the 12-digit UPI Reference / UTR Number from your UPI payment receipt:',
        input: 'text',
        inputPlaceholder: 'e.g. 123456789012',
        showCancelButton: true,
        confirmButtonText: 'Verify Now',
        showLoaderOnConfirm: true,
        inputAttributes: {
            maxlength: '22',
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        preConfirm: (utr) => {
            if (!utr || utr.trim().length < 4) {
                Swal.showValidationMessage('Please enter a valid 12-digit UTR number');
                return false;
            }
            return $.ajax({
                type: "post",
                url: "/order2/payment-status",
                data: {
                    byte_order_status: <?php echo json_encode($cxrmerchantTransactionId); ?>,
                    utr: utr.trim()
                }
            }).then(response => {
                var res = (response || "").toString().trim().toLowerCase();
                if (res === "success" || res.indexOf("success") !== -1) {
                    return utr.trim();
                } else {
                    throw new Error('Verification failed. Please check UTR and try again.');
                }
            }).catch(error => {
                Swal.showValidationMessage(error.message || 'Verification failed');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            handleSuccess(result.value);
        }
    });
}
</script>
</body>
</html>
