<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/pages/dbFunctions.php';

$order_id = $_REQUEST['order_id'] ?? '';
$hash = $_REQUEST['hash'] ?? '';
$status = $_REQUEST['status'] ?? 'SUCCESS';
$utr = $_REQUEST['utr'] ?? '';

if (!empty($order_id) && function_exists('notify_merchant_callback')) {
    notify_merchant_callback($order_id, $status, $utr);
}

$url = $murl . "/api/order-info";

$data = array(
    "hash" => $hash,
    "order_id" => $order_id,
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json",
        'method'  => 'POST',
        'content' => json_encode($data),
    ),
);

$context  = stream_context_create($options);
 $response = file_get_contents($url, false, $context);

 $data = json_decode($response,true);
 
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payments Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Poppins';
            font-size: 22px;
        }

        .form-title {
            text-align: center;
        }

        .table-header-dark {
            background-color: #343a40;
            color: white;
        }

        .centered-button {
            text-align: center;
            margin-top: 20px; /* Add margin to separate buttons */
        }
    </style>
</head>
<body>
    <div style="margin-top: 40px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-sm-12">
                    <h2 class="form-title mb-4"><b>Payment Confirmation</b></h2>
                      <div class="text-center"> <!-- Center the content within the column -->
                        <img src="https://cdn.dribbble.com/users/614270/screenshots/14575431/media/4907a0869e9ed2ac4e2d1c2beaf9f012.gif" alt="Responsive Image" class="img-fluid">
                    </div>
                    <p><b>Message: <?php echo $data['message']; ?></b></p>
                    <p><b>Amount: ₹<?php echo $data['amount']; ?></b></p>
                    <p><b>UTR NO: <?php echo $data['utr']; ?></b></p>
                    <p><b>Date: <?php echo $data['date']; ?></b></p>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS (Optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
