<?php
require_once __DIR__ . '/../config.php';

// Set the API endpoint URL
$api_url = $murl . '/api/create-order';

// Define the payload data
$o = 'DEMO' . rand(10000, 99999);

$data = array(
    'customer_mobile' => '9876543210',
    'user_token' => 'admin_token_demo_1234567890',
    'amount' => '100',
    'order_id' => $o,
    'redirect_url' => $murl . '/pay/callback.php',
    'remark1' => 'Test Payment',
    'remark2' => 'Demo Gateway',
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Encode the data as form-urlencoded
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

// Execute the cURL request
echo $response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Parse the JSON response
    $result = json_decode($response, true);

    // Check if the status is true or false
    if ($result && isset($result['status'])) {
        if ($result['status'] === true) {
            // Order was created successfully
            echo 'Order Created Successfully<br>';
            echo 'Order ID: ' . $result['result']['orderId'] . '<br>';
            echo 'Payment URL: ' . $result['result']['payment_url'];
        } else {
            // Plan expired
            echo 'Status: ' . $result['status'] . '<br>';
            echo 'Message: ' . $result['message'];
        }
    } else {
        // Invalid response
        echo 'Invalid API response';
    }
}

// Close cURL session
curl_close($ch);
?>
