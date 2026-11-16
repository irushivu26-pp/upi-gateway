<?php
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";
include "../pages/function.php";
include "../partner/config.php";
date_default_timezone_set('Asia/Kolkata');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    $json = array("status" => "Access Denied", "msg" => "Unauthorized Access");
    echo json_encode($json);
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    // Validate incoming POST data
    $required_fields = ['customer_mobile', 'user_token', 'amount', 'order_id', 'redirect_url', 'remark1', 'remark2'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(array("status" => "FAILURE", "msg" => "Please provide $field data"));
            exit;
        }
    }

    $customer_mobile = $_POST['customer_mobile'];
    $user_token = $_POST["user_token"];
    $amount = $_POST["amount"];
    $order_id = $_POST["order_id"];
    $redirect_url = $_POST["redirect_url"];
    $remark1 = $_POST["remark1"];
    $remark2 = $_POST["remark2"];


    $check_order_id_query = "SELECT * FROM orders WHERE order_id='$order_id' AND user_token='$user_token'";
    $existing_order_result = getXbyY($check_order_id_query);
    if (!empty($existing_order_result)) {
        echo json_encode(array("status" => "false", "message" => "Order ID already exists for this user"));
        exit;
    }

    // Fetch user details
    $user_details_query = "SELECT * FROM users WHERE user_token='$user_token'";
    $user_details = getXbyY($user_details_query);

    if (!$user_details || !isset($user_details[0]['phonepe_connected']) || !$user_details[0]['phonepe_connected'] == "Yes") {
        echo json_encode(array("status" => "false", "message" => "PhonePe not connected for this user"));
        exit;
    }

    $today = date('Y-m-d');
    $expire_date = $user_details[0]['expiry'];
    $upi_id = $user_details[0]['upi_id'];
    $name = $user_details[0]['name'];

    if ($expire_date < $today) {
        echo json_encode(array("status" => "false", "message" => "Your Plan has Expired. Please Renew."));
        exit;
    }

    $link_token = generateUniqueToken();
    $byteorderid = rand(11111, 99999) . time();
    $gateway_txn = uniqid();
    $currentTimestamp = date('Y-m-d H:i:s');


    $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$currentTimestamp')";
    setXbyY($sql_insert_link);

 function generateQRCode($text, $size = 200) {
    // Create a blank image for the QR code
    $qr_code_image = imagecreate($size, $size);

    // Allocate background color (white)
    $background_color = imagecolorallocate($qr_code_image, 255, 255, 255);

    // Allocate text color (black)
    $text_color = imagecolorallocate($qr_code_image, 0, 0, 0);

    // Fill the image with the background color
    imagefill($qr_code_image, 0, 0, $background_color);

    // Create a new true color image for the QR code
    $qr_code = imagecreatetruecolor($size, $size);

    // Fill the QR code image with the background color
    imagefill($qr_code, 0, 0, $background_color);

    // Allocate QR code color (black)
    $qr = imagecolorallocate($qr_code, 0, 0, 0);

    // Add text to the QR code image
imagettftext($qr_code_image, 10, 0, 75, 125, $text_color, '/Arial.ttf', $text);
;

    // Return the QR code image
    return $qr_code_image;
}

    $payment_link = "upi://pay?pa=$upi_id&am=$amount&pn=$name&tn=$byteorderid&tr=$byteorderid";

    // Generate QR code image
    $qr_code_image = generateQRCode($payment_link);

    // Convert image to base64
    ob_start();
    imagepng($qr_code_image);
    $image_data = ob_get_clean();
    $payment_image_base64 = base64_encode($image_data);

    // Function to generate QR code
   

    $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id) 
            VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'PhonePe', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '{$user_details[0]['id']}')";
    setXbyY($sql);

    echo json_encode(array("status" => true, "message" => "Order Created Successfully", "qr_pay" => $payment_image_base64));
    
}

?>
