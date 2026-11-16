<?php
error_reporting(0);
include "header.php"; ?>
 <style>
        .whatsapp-link {
            text-decoration: none;
        }

        .whatsapp-button {
            padding: 10px 20px;
            background-color: #25D366;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: inline-block;
        }

        .whatsapp-button:hover {
            background-color: #1DA851;
        }
    </style>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-ui-checks"></i> Subscription & Plan</h1>
          
        </div>
      </div>
      <div class="row">

        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">

<div class="alert alert-danger">
<span data-notify="icon" class="la la-bell"></span>
<b>Note:</b> Your old plan will be automatically deactivated after purchasing the new plan and is non-refundable.
</div>						<div class="row">							
											
							<div class="col-md-3">
								<div class="card text-center">
									<div class="card-header">
										<h4 class="card-title">Trail</h4>
										<h2 class="text-center">₹50</h2>
										<p class="card-category">1 Month</p>
									</div>
									<div class="card-body">
										
											<table class="mx-auto">
										<tbody>
										  <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>0 Transaction Fee</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>Realtime Transaction</p></td>
                                            </tr>
                                            <tr>
                                               <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>No Amount Limit</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p style="color:red;">HDFC Vyapar</p></td>
                                            
                                            </tr>                                              
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Dynamic QR Code</p></td>
                                            </tr>
                                                                                      
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Direct UPI Intent</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Accept All UPI Apps</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>24*7 WhatsApp Support</p></td>
                                            </tr>
										</tbody>
										</table>
									</div>
									<!--<div class="card-footer">
										<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
											<input type="hidden" name="amount" value="50">
											<!--<button name="paytm" class="btn btn-outline-success btn-block">Buy Now</button>-->
											<!--button name="upiapi" class="btn btn-outline-primary btn-block">Buy Now</button-->
											<!--<button name="upigate" class="btn btn-outline-success btn-block">Buy Now</button>
									
										</form>
									</div>-->
									
											<!--<button name="paytm" class="btn btn-outline-success btn-block">Buy Now</button>-->
											<!--button name="upiapi" class="btn btn-outline-primary btn-block">Buy Now</button-->
											<a href="https://wa.me/919506912898" class="whatsapp-link">
        <button class="whatsapp-button">Chat on WhatsApp</button>
    </a>
									
									</div>
								</div>
							</div>			
											
							<div class="col-md-3">
								<div class="card text-center">
									<div class="card-header">
										<h4 class="card-title">Starter</h4>
										<h2 class="text-center">₹299</h2>
										<p class="card-category">3 Month</p>
									</div>
									<div class="card-body">
										
											<table class="mx-auto">
										<tbody>
										  <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>0 Transaction Fee</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>Realtime Transaction</p></td>
                                            </tr>
                                            <tr>
                                               <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>No Amount Limit</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p style="color:red;">HDFC Vyapar</p></td>
                                            
                                            </tr>                                              
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Dynamic QR Code</p></td>
                                            </tr>
                                                                                      
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Direct UPI Intent</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Accept All UPI Apps</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>24*7 WhatsApp Support</p></td>
                                            </tr>
										</tbody>
										</table>
									</div>
									<div class="card-footer">
										<a href="https://wa.me/919506912898" class="whatsapp-link">
        <button class="whatsapp-button">Chat on WhatsApp</button>
    </a>
									</div>
								</div>
							</div>			
											
							<div class="col-md-3">
								<div class="card text-center">
									<div class="card-header">
										<h4 class="card-title">Business</h4>
										<h2 class="text-center">₹449</h2>
										<p class="card-category">6 Month</p>
									</div>
									<div class="card-body">
										
											<table class="mx-auto">
										<tbody>
										  <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>0 Transaction Fee</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>Realtime Transaction</p></td>
                                            </tr>
                                            <tr>
                                               <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>No Amount Limit</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p style="color:red;">HDFC Vyapar</p></td>
                                            
                                            </tr>                                              
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Dynamic QR Code</p></td>
                                            </tr>
                                                                                      
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Direct UPI Intent</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Accept All UPI Apps</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>24*7 WhatsApp Support</p></td>
                                            </tr>
										</tbody>
										</table>
									</div>
									<div class="card-footer">
										<a href="https://wa.me/919506912898" class="whatsapp-link">
        <button class="whatsapp-button">Chat on WhatsApp</button>
    </a>
									</div>
								</div>
							</div>			
											
							<div class="col-md-3">
								<div class="card text-center">
									<div class="card-header">
										<h4 class="card-title">Enterprise</h4>
										<h2 class="text-center">₹699</h2>
										<p class="card-category">12 Month</p>
									</div>
									<div class="card-body">
										<table class="mx-auto">
										<tbody>
										  <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>0 Transaction Fee</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>Realtime Transaction</p></td>
                                            </tr>
                                            <tr>
                                               <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>No Amount Limit</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p style="color:red;">HDFC Vyapar</p></td>
                                            
                                            </tr>                                              
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Dynamic QR Code</p></td>
                                            </tr>
                                                                                      
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Direct UPI Intent</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-danger me-2" data-feather="x"></i></td>
                                                <td><p>Accept All UPI Apps</p></td>
                                            </tr>
                                            <tr>
                                                <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                <td><p>24*7 WhatsApp Support</p></td>
                                            </tr>
										</tbody>
										</table>
									</div>
									<div class="card-footer">
										<a href="https://wa.me/919506912898" class="whatsapp-link">
        <button class="whatsapp-button">Chat on WhatsApp</button>
    </a>
									</div>
								</div>
							</div>	
							
							
							</div>
							
							<?php
							
// Function to sanitize user input
function sanitizeInput($input) {
    if (is_string($input)) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    } else {
        // Handle non-string input here (e.g., arrays, objects, etc.) if needed.
        return $input;
    }
} 

							

if(isset($_REQUEST['upigate'])){

// Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }
    
    
		    
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
require_once('lib/config.php');
require_once('lib/checksum.php');


$checkSum = "";
$paramList = array();

$upiuid = $upiuid; // Its UPI Unique ID, (Url:http://example.com/Settings).
$orderId = "WETECH".rand(00000000,1000000);
$txnAmount = sanitizeInput($_POST['amount']);
$txnNote = $userdata['email'];
$callback_url = "https://$server/partner/txnResult.php";
$cust_Mobile = $userdata['mobile'];
$cust_Email = $userdata['email'];
// Create an array having all required parameters for creating checksum.




$paramList["upiuid"] = $upiuid;
$paramList["token"] = $token;
$paramList["orderId"] = $orderId ;
$paramList["txnAmount"] = $txnAmount;
$paramList["txnNote"] = $txnNote;
$paramList["cust_Mobile"] = $cust_Mobile;
$paramList["cust_Email"] = $cust_Email;
$paramList["callback_url"] = $callback_url;

$checkSum = RechPayChecksum::generateSignature($paramList,$secret);
?>
<html>
<head>
<title>Gateway Check Out Page</title>
</head>
<body>
	<center><h1>Please do not refresh this page...</h1></center>
		<form method="post" action="<?php echo $UPBPAY_TXN_URL ?>" name="f1">
		<table border="1">
			<tbody>
			<?php
			foreach($paramList as $name => $value) {
				echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
			}
			?>
			<input type="hidden" name="checksum" value="<?php echo $checkSum ?>">
			</tbody>
		</table>
		<script type="text/javascript">
			document.f1.submit();
		</script>
	</form>
</body>
</html>
<?php } ?>

</div>
</div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <!-- Page specific javascripts-->
    <!-- Google analytics script-->
    
  </body>
</html>
