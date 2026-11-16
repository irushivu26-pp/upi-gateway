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
    <title>Fastest & Secured UPI Gateway|| UPB GROUP</title>
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



$link_token = $_GET["token"];

// Fetch order_id based on the token from the payment_links table
$sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '$link_token'";
$result = getXbyY($sql_fetch_order_id);

if (count($result) === 0) {
    // Token not found or expired
    echo "Token not found or expired";
    exit;
}

$order_id = $result[0]['order_id'];
$created_at = strtotime($result[0]['created_at']);
$current_time = time();

// Check if the token has expired (more than 5 minutes)
if (($current_time - $created_at) > (5 * 60)) {
    echo "Token has expired";
    exit;
}


$slq_p = "SELECT * FROM orders where order_id='$order_id'";
$res_p = getXbyY($slq_p);    
$amount = $res_p[0]['amount'];
$user_token = $res_p[0]['user_token'];
$redirect_url = $res_p[0]['redirect_url'];
$cxrkalwaremark = $res_p[0]['byteTransactionId'];  //remark
//$cxrbytectxnref=$res_p[0]['paytm_txn_ref'];

if($redirect_url==''){
$redirect_url=$murl . '/';    
}



    
$slq_p = "SELECT * FROM bharatpe_tokens where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $upi_id = $res_p[0]['Upiid']; //upi id from paytm tokens
 
 $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $unitId=$res_p[0]['name'];
 
 $asdasd23="ARC".rand(111,999).time().rand(1,100);
 $cxrbytectxnref=time().rand(11111,99999);
 
 $orders="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$cxrmerchantTransactionId";
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($orders);

// Fetch the image content from the URL safely
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
                   
                    <table style=" margin: 0 auto; background-color: #fff; border-radius: 8px">
                      <tbody>
                        <tr>
                          <td style="padding: 30px"> 
                        <div class="col-md-6">
<div class="text-dark">
    <b><?php echo $unitId?></b><br></div>
     <div class="text-dark">Amount<b> <?php echo '₹'. $amount;?></b></div>
            </div> 
<div class="col-md-12 text-center">
<hr>
<div class="text-dark"><b>Scan QR code using BHIM or your preferred UPI app</b></div>

<div class="col-md-12 text-center mt-1">
<div class="text-center">
<img src="/assets/img/gpay.png" alt="gpay" height="20px"> 
<img src="/assets/img/paytm.png" alt="gpay" height="20px">
<img src="/assets/img/phonepe.png" alt="gpay" height="20px">
<img src="/assets/img/bhim_logo.png" alt="gpay" height="20px">
<img src="/assets/img/amazonpay.png" alt="gpay" height="20px">
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
        <a href="<?php echo $orders; ?>" class="btn btn-sm btn-outline-primary d-md-none"><i class="fas fa-external-link-alt"></i> Open in UPI App</a>
    </div>
</div>
            <button class="pay-button" onclick="payViaUPI()">Confirm Payment</button>

<div class="text-center font-weight-bold mt-2">
<div class="text-center font-weight-bold mt-1">
    This QR code will expire in <b><span id="timeout"></span></b>
</div>

</div> 
</div>
<div class="col-md-12 text-center mt-0">
</div>
</div>
</div>
</div>
</div>
 <form id="paymentForm" action="/instantpay/bharatpe/payment-verify" method="post">
    <!-- Other input fields -->
    <input type="hidden" name="TransactionId" value="<?php echo $cxrkalwaremark; ?>">
    <input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>">
</form>
<script>    
function payViaUPI() {
    document.getElementById('paymentForm').submit();
}
</script>
             
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
  </body>
<?php 
$html = '
<form id="formSubmit" action="'.$redirect_url.'" method="post" style="display:none;">
    <input type="hidden" name="order_id" value="'.$order_id.'">
    <input type="hidden" name="hash" value="'.$hash.'">
</form>
<script>
function upiCountdown(elm, minute, second, url) {
    document.getElementById(elm).innerHTML = minute + ":" + second;
    startTimer();

    function startTimer() {
        var presentTime = document.getElementById(elm).innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = timeArray[0];
        var s = checkSecond((timeArray[1] - 1));
        if(s == 59){m = m - 1}
        if(m < 0){
            Swal.fire({
              title: "Oops",
              text: "Transaction Timeout!",
              icon: "error",
            });
            window.location.href = "'.$server_url.'";
        }
        document.getElementById(elm).innerHTML = m + ":" + s;
        setTimeout(startTimer, 1000);
    }

    function checkSecond(sec) {
        if (sec < 10 && sec >= 0) { sec = "0" + sec };
        if (sec < 0) { sec = "59" };
        return sec;
    }
}

upiCountdown("timeout", 5, 0, location.href);
function check() {
    $.ajax({
        type: "post",
        url: "/order4/payment-status",
        data: "byte_order_status='.$cxrmerchantTransactionId.'",
        success: function(data) {
            if (data == "success") {
                // Display a success message using SweetAlert
                Swal.fire({
                    title: "",
                    text: "Your Payment Received Successfully",
                    icon: "success",
                });
                setTimeout(function(){ 
                    document.getElementById("formSubmit").submit();
                }, 2000);
            } else if (data == "FAILURE") {
                Swal.fire({
                    title: "",
                    text: "Your Payment Was Failed",
                    icon: "error",
                });
                window.location.href = "'.$server_url.'";
            }
        }
    });
}

setInterval(check, 3000);
</script>
';

echo $html;
?>

   

</body>
</html>